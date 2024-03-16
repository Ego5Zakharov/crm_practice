<?php

namespace App\Kernel\Database;

use App\Kernel\Collections\Collection;
use App\Kernel\Database\Concerns\HasAttributes;
use App\Kernel\Database\Concerns\HasRelationships;
use App\Kernel\Database\Query\Exceptions\WhereOperatorNotFoundException;
use App\Kernel\Database\Query\Queries;
use App\Kernel\Database\Support\Arrayable;
use PDO;
use PDOStatement;

abstract class Model implements Arrayable
{
    use HasRelationships, HasAttributes, Queries;

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

    public function __construct(array|Model $data = [])
    {
        $this->database = Database::getInstance();

        self::$instance = $this;

        foreach ($data as $key => $value) {
            $this->$key = $value;
        }

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

        $query = "INSERT INTO $this->table ($columns) VALUES ($placeholders)";

        $this->statement = $this->database::$pdo->prepare($query);

        $this->statement->execute($original);

        $model = $this->find(
            $this->database::$pdo->lastInsertId()
        );

        $this->original = $model->original;
        $this->setAttributes($model->attributes);

        return $model;
    }

    public function find(int $id): Model|static|null
    {
        $query = "SELECT * FROM $this->table WHERE id = :id";

        $this->statement = $this->database::$pdo->prepare($query);

        $this->statement->bindParam('id', $id);

        $original = $this->statement->execute() ? $this->statement->fetch(PDO::FETCH_ASSOC) : null;

        if (!$original) {
            return null;
        }

        return new $this($original);
    }

    public function update(): bool
    {
        $original = $this->toArray();

        $setClause = [];

        foreach ($original as $key => $item) {
            $setClause[] = "$key = :$key";
        }

        $setClause = implode(',', $setClause);

        $query = "UPDATE $this->table SET $setClause WHERE id = :id";

        $this->statement = $this->database::$pdo->prepare($query);

        return $this->statement->execute($original);
    }

    public function limit(int $count = 12): static
    {
        $this->limitCount = $count;

        return $this;
    }

    public function delete(): bool
    {
        $data = $this->toArray();

        $query = "DELETE FROM $this->table WHERE id = :id";

        $this->statement = $this
            ->database
            ->getPDO()
            ->prepare($query);

        $this->statement->bindParam('id', $data['id']);

        return $this->statement->execute();
    }

    // TODO добавить выборку из аргументов в селекте
    public function select($table): string
    {
        if (!$this->limitCount) {
            return "SELECT * FROM $table";
        }

        return "SELECT * FROM $table LIMIT $this->limitCount";
    }

    public function getWithoutBindings(): false|array
    {
        $this->query = "{$this->select($this->table)}";

        $statement = $this->prepareQuery();

        return $statement->execute()
            ? $statement->fetchAll(PDO::FETCH_ASSOC)
            : [];
    }

    public function get(): ?Collection
    {
        $query = $this->prepareQuery();

        if (!$query) {
            $models = [];

            $statementResult = $this->getWithoutBindings();

            foreach ($statementResult as $key => $value) {
                $clonedModel = clone $this;

                $clonedModel->setAttributes($value);
                $clonedModel->setOriginals($value);

                $models[] = $clonedModel;
            }

            return collect($models);
        }

        $statementResult = $this->statement->execute($this->getBindParams())
            ? $this->statement->fetchAll(PDO::FETCH_ASSOC)
            : null;

        if (!$statementResult) {
            return null;
        }

        $models = [];

        foreach ($statementResult as $data) {
            $this->setAttributes($data);
            $this->setOriginals($data);

            $clonedModel = clone $this;
            $models[] = $clonedModel;
        }

        return collect($models);
    }

    public function prepareQuery(): false|PDOStatement|null
    {
        return $this->query
            ? $this->database::$pdo->prepare($this->query)
            : null;
    }

    public function first(): array|static
    {
        $this->prepareQuery();

        $statementResult = $this->statement->execute($this->getBindParams())
            ? $this->statement->fetch(PDO::FETCH_ASSOC)
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
        if (!in_array($operator, $this->getWhereOperators())) {
            throw new WhereOperatorNotFoundException("$operator does not exist.");
        }

        $this->incrementWhereCallsCount();

        $paramName = ":param" . $this->getWhereCallsCount();

        if (str_contains($this->query, 'WHERE')) {
            $this->concatQuery(" AND $key $operator $paramName");
        } else {
            $this->query = "{$this->select($this->table)} WHERE $key $operator $paramName";
        }

        $this->bindParam($paramName, $value);

        return $this;
    }

    public static function query(): static
    {
        return self::$instance = new static();
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
        return $this->attributes;
    }
}