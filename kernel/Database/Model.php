<?php

namespace App\Kernel\Database;

use App\Kernel\Collections\Collection;
use App\Kernel\Database\Concerns\HasAttributes;
use App\Kernel\Database\Concerns\HasRelationships;
use App\Kernel\Database\Query\Builder;
use App\Kernel\Database\Query\Exceptions\WhereOperatorNotFoundException;
use App\Kernel\Database\Support\Arrayable;
use App\Kernel\Pagination\LengthAwarePaginator;
use Dotenv\Dotenv;
use PDO;

abstract class Model implements Arrayable
{
    use HasRelationships, HasAttributes;

    // первичный ключ
    protected string $primaryKey = "id";

    // подключение к бд
    protected Database $database;

    // переменные которые подлежат заполнению в таблице
    protected array $fillable = [];

    // название таблицы
    protected string $table = "";

    // копия текущей модели
    protected static ?Model $instance = null;

    // разрешено ли массовое заполнение
    protected bool $guard = true;

    // какие связи модели подгружать сразу
    protected array $with = [];

    protected ?Builder $builder;

    public function __construct(array|Model $data = [])
    {
        $this->database = Database::getInstance();

        $this->builder = new Builder($this->database);

        foreach ($data as $key => $value) {
            $this->$key = $value;
        }

        self::$instance = $this;
    }

    public function __get(string $value)
    {
        // подгружаем связи
        if (method_exists($this, $value)) {
            $this->relations[$value] = $this->$value();
            return $this->relations[$value];
        }

        if (array_key_exists($value, $this->original)) {
            return $this->original[$value];
        } else if (array_key_exists($value, $this->getAttributes())) {
            return $this->getAttribute($value);
        }

        return null;
    }

    public function __set(mixed $key, mixed $value)
    {
        if ($this->guard && in_array($key, $this->fillable)) {

            $this->original[$key] = $value;
        } else if (!$this->guard) {

            $this->original[$key] = $value;
        }

        $this->setAttribute($key, $value);
    }


    public function create(array $data = []): Model|static|null
    {
        if (empty($data)) {
            return $this->createWithoutData();
        }

        return $this->createWithData($data);
    }

    private function createWithData(array $data): Model|static|null
    {
        $columns = implode(', ', array_keys($data));

        $placeholders = ":" . implode(', :', array_keys($data));

        $this->builder->setQuery("INSERT INTO {$this->getTable()} ($columns) VALUES ($placeholders)");
        $this->builder->prepareQuery();
        $this->builder->setBindParams($data);
        $this->builder->execute();

        return $this->find($this->builder->getDatabase()->getPDO()->lastInsertId());
    }

    private function createWithoutData(): Model|static|null
    {
        $original = $this->toArray();

        $columns = implode(', ', array_keys($original));

        $placeholders = ':' . implode(', :', array_keys($original));

        $this->builder->setQuery(
            "INSERT INTO {$this->getTable()} ($columns) VALUES ($placeholders)"
        );

        $this->builder->prepareQuery();

        $this->builder->setBindParams($original);

        $this->builder->getStatement()->execute($this->builder->getBindParams());

        return $this->find(
            $this->builder->getDatabase()::$pdo->lastInsertId()
        );
    }

    public function find(Model|int $param): Model|static|null
    {
        $this->builder->setQuery("{$this->select($this->table)} WHERE id = :id");

        if ($param instanceof Model) {
            $param = $param->getAttribute('id');
        }

        $this->builder->prepareQuery();

        $this->builder->getStatement()->bindParam(':id', $param);

        $original = $this->builder->getStatement()->execute()
            ? $this->builder->getStatement()->fetch(PDO::FETCH_ASSOC)
            : null;

        if (!$original) {
            return null;
        }

        return new static($original);
    }

    public function update(array $data = []): bool
    {
        if (empty($data)) {
            return $this->updateWithoutData();
        }

        return $this->updateWithData($data);
    }

    private function updateWithData(array $data): bool
    {
        $setClause = [];

        foreach ($data as $key => $value) {
            $setClause[] = "$key = :$key";
        }

        $placeholders = implode(', ', $setClause);

        $this->builder->setQuery("UPDATE {$this->getTable()} SET $placeholders WHERE id = :id");
        $this->builder->prepareQuery();
        $this->builder->setBindParams($data);
        $this->builder->bindParam(':id', $this->getAttribute('id'));

        return $this->builder->pureExecute();
    }

    // обновление данных о пользователе
    public function fresh(): Model|static|null
    {
        return $this->find($this->getAttribute('id'));
    }

    private function updateWithoutData(): bool
    {
        $attributes = $this->toArray();

        $setClause = [];

        foreach ($attributes as $key => $item) {
            $setClause[] = "$key = :$key";
        }

        $setClause = implode(',', $setClause);

        $this->builder->setQuery("UPDATE {$this->getTable()} SET $setClause WHERE id = :id");

        $this->builder->prepareQuery();

        return $this->builder->getStatement()->execute($attributes);
    }

    public function limit(int $count = 12): static
    {
        $this->builder->setLimitCount($count);
        return $this;
    }

    public function delete(): bool
    {
        $data = $this->toArray();

        $this->builder->setQuery(
            "DELETE FROM {$this->getTable()} WHERE id = :id"
        );

        $this->builder->prepareQuery();

        $this->builder->getStatement()->bindParam('id', $data['id']);

        return $this->builder->getStatement()->execute();
    }

    // TODO добавить выборку из аргументов в селекте
    public function select($table): string
    {
        return "SELECT * FROM {$this->getTable()}";
    }

    private function selectWithoutBindings(): string
    {
        return "SELECT * FROM {$this->getTable()}";
    }

    public function paginate(int $perPage = 12, int $page = 1): array
    {
        $builder = $this->newBuilder();

        $builder->setQuery("SELECT COUNT(*) as total FROM {$this->getTable()}");

        $builder->prepareQuery();

        $totalCount = $builder->execute()->fetch()['total'];

        if (!$totalCount) {
            return [];
        }

        // какое количество элементов пропустить
        $offset = ($page - 1) * $perPage;

        $this->builder->setQuery("{$this->select($this->table)} LIMIT $offset, $perPage");
        $this->builder->prepareQuery();

        $fetchData = $this->builder->execute()->fetchAll();

        $models = [];

        foreach ($fetchData as $key => $value) {
            $clonedModel = clone $this;
            $clonedModel->setAttributes($value);
            $clonedModel->setOriginals($value);
            $clonedModel->with($this->getWithRelations());
            $clonedModel->unsetWithRelationsKeys();

            $models[] = $clonedModel;
        }

        $collection = collect($models);
        $paginator = new LengthAwarePaginator($collection, $perPage, 1);

        $paginator->setTotal($totalCount);
        $paginator->setTotalPages($totalCount / $perPage);
        $paginator->setCurrentPage($page);
        $paginator->metaConfiguration($page);

        return $paginator->getInfo();
    }

    private function newBuilder(): Builder
    {
        return new Builder($this->database);
    }

    // если вызывается метод с where, limit и тд - вызываем getWithParams
    // иначе - getWithoutBindings
    public function get(): Collection|false|array
    {
        if ($this->builder->getQuery()) {
            return $this->getWithParams();
        }

        return $this->getWithoutBindings();
    }

    private function getWithParams(): Collection|array
    {
        if ($this->builder->getLimitCount()) {
            $this->builder->concatQuery(" LIMIT {$this->builder->getLimitCount()}");
        }

        $this->builder->prepareQuery();

        $fetchData = $this->builder->getStatement()->execute($this->builder->getBindParams())
            ? $this->builder->getStatement()->fetchAll(PDO::FETCH_ASSOC)
            : null;

        if (!$fetchData) {
            return [];
        }

        $models = [];

        foreach ($fetchData as $key => $value) {
            $clonedModel = clone $this;

            $clonedModel->setOriginals($value);
            $clonedModel->setAttributes($value);
            $clonedModel->with($this->getWithRelations());
            $clonedModel->unsetWithRelationsKeys();

            $models[] = $clonedModel;
        }

        return collect($models);
    }

    private function getWithoutBindings(): array|Collection
    {
        $this->builder->setQuery("{$this->select($this->table)}");

        $this->builder->prepareQuery();

        $fetchData = $this->builder->getStatement()->execute()
            ? $this->builder->getStatement()->fetchAll(PDO::FETCH_ASSOC)
            : [];

        if (!$fetchData) {
            return [];
        }

        $models = [];

        foreach ($fetchData as $key => $value) {
            $clonedModel = clone $this;

            $clonedModel->setOriginals($value);
            $clonedModel->setAttributes($value);
            $clonedModel->with($this->getWithRelations());
            $clonedModel->unsetWithRelationsKeys();

            $models[] = $clonedModel;
        }

        return collect($models);
    }

    // если вызывается метод с where, limit и тд - вызываем firstWithParams
    // иначе - firstWithoutBindings
    public function first(): array|static|null
    {
        if ($this->builder->getQuery()) {
            return $this->firstWithParams();
        }

        return $this->firstWithoutBindings();
    }

    private function firstWithParams(): null|static
    {
        $this->builder->concatQuery(" LIMIT 1");

        $this->builder->prepareQuery();

        $fetchData = $this->builder->getStatement()->execute($this->builder->getBindParams())
            ? $this->builder->getStatement()->fetch(PDO::FETCH_ASSOC)
            : null;

        if (!$fetchData) {
            return null;
        }

        $model = new $this($fetchData);
        $model->with($this->getWithRelations());
        $model->unsetWithRelationsKeys();

        return $model;
    }

    private function firstWithoutBindings(): array|static
    {
        $this->builder->setQuery("{$this->select($this->table)} ORDER BY id ASC LIMIT 1");

        $this->builder->prepareQuery();

        $statementResult = $this->builder->getStatement()->execute()
            ? $this->builder->getStatement()->fetch(PDO::FETCH_ASSOC)
            : null;

        if (!$statementResult) {
            return [];
        }

        $model = new $this($statementResult);
        $model->with($this->getWithRelations());
        $model->unsetWithRelationsKeys();

        return $model;
    }

    public function where(string $key, mixed $operator, mixed $value): ?static
    {
        try {
            if (!in_array($operator, $this->builder->getWhereOperators())) {
                throw new WhereOperatorNotFoundException("$operator does not exist.");
            }

            $this->builder->incrementWhereCallsCount();

            $paramName = ":param" . $this->builder->getWhereCallsCount();

            if (str_contains($this->builder->getQuery(), 'WHERE')) {
                $this->builder->concatQuery(" AND $key $operator $paramName");
            } else {
                $this->builder->setQuery("{$this->select($this->table)} WHERE $key $operator $paramName");
            }

            $this->builder->bindParam($paramName, $value);
        } catch (WhereOperatorNotFoundException $exception) {
            // TODO Log Exceptions
        }

        return $this;
    }

    public function with(array $relations = []): void
    {
        foreach ($relations as $index => $relation) {
            if (method_exists($this, $relation)) {
                $this->setWithRelation($relation, $this->$relation);
            }
        }
    }

    public static function query(): static
    {
        return self::$instance = new static();
    }

    public function freshQuery(): void
    {
        $this->builder->setQuery("");
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function getWithRelations(): array
    {
        return $this->with;
    }

    public function getWithRelation(string $key)
    {
        return $this->with[$key];
    }

    public function collect(): Collection
    {
        return collect($this->attributes);
    }

    // удаляет все индексы которые имеею структуру не
    //    [
    //      'relation'=>['relationData']
    //    ]
    public function unsetWithRelationsKeys(): void
    {
        foreach (array_keys($this->getWithRelations()) as $index) {
            if (is_numeric($index)) {
                unset($this->with[$index]);
            }
        }
    }

    public function newQuery(): static
    {
        return $this::query();
    }

    public function toArray(): array
    {
        return $this->getAttributes();
    }
}