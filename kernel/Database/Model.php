<?php

namespace App\Kernel\Database;

use App\Kernel\Collections\Collection;
use App\Kernel\Database\Concerns\HasAttributes;
use App\Kernel\Database\Concerns\HasRelationships;
use App\Kernel\Database\Query\Builder;
use App\Kernel\Database\Query\Exceptions\WhereOperatorNotFoundException;
use App\Kernel\Database\Support\Arrayable;
use PDO;

abstract class Model implements Arrayable
{
    use HasRelationships, HasAttributes;

    // первичный ключ
    protected string $primaryKey = "id";

    protected ?Builder $builder;

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

    public function __construct(array|Model $data = [])
    {
        $this->database = Database::getInstance();

        $this->builder = new Builder($this->database);

        foreach ($data as $key => $value) {
            $this->$key = $value;
        }

        self::$instance = $this;

        // relations
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


    public function create(): Model|static|null
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

        $model = $this->find(
            $this->builder->getDatabase()::$pdo->lastInsertId()
        );

        $this->setOriginals($model->original);
        $this->setAttributes($model->attributes);

        return $model;
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

    public function update(): bool
    {
        $original = $this->toArray();

        $setClause = [];

        foreach ($original as $key => $item) {
            $setClause[] = "$key = :$key";
        }

        $setClause = implode(',', $setClause);

        $this->builder->setQuery("UPDATE {$this->getTable()} SET $setClause WHERE id = :id");

        $this->builder->prepareQuery();

        return $this->builder->getStatement()->execute($original);
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
        if (!$this->builder->getLimitCount()) {
            return "SELECT * FROM {$this->getTable()}";
        }

        return "SELECT * FROM {$this->getTable()} LIMIT {$this->builder->getLimitCount()}";
    }

    public function selectWithoutBindings(): string
    {
        return "SELECT * FROM {$this->getTable()}";
    }

    public function paginate(int $perPage = 12, int $page = 1)
    {
        // получить общее количество
        // получать perPage количество элементов из запроса
        $totalCount = $this->select();

        $this->setQuery($this->select($this->table));
        dd($this->getQuery());
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

        return new static($fetchData);
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

        $this->setAttributes($statementResult);
        $this->setOriginals($statementResult);

        return $this;
    }


    /**
     * @throws WhereOperatorNotFoundException
     */
    public function where(string $key, mixed $operator, mixed $value): ?static
    {
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

        return $this;
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

    public function collect(): Collection
    {
        return collect($this->attributes);
    }

    public function toArray(): array
    {
        return $this->getAttributes();
    }
}