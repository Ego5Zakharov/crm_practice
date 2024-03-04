<?php

namespace App\Kernel\Database;

use App\Kernel\Database\Concerns\HasAttributes;
use App\Kernel\Database\Concerns\HasRelationships;
use App\Kernel\Database\Query\Queries;
use App\Kernel\Database\Support\Arrayable;
use PDO;

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

    // разрешено ли массовое заполнение
    protected bool $guard = true;

    // какие связи модели подгружать сразу
    protected array $with = [];

    public function __construct(array|Model $data = [])
    {
        $this->database = Database::getInstance();

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

        $statement = $this->database::$pdo->prepare($query);

        $statement->execute($original);

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

        $statement = $this->database::$pdo->prepare($query);

        $statement->bindParam('id', $id);

        $original = $statement->execute() ? $statement->fetch(PDO::FETCH_ASSOC) : null;

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

        $statement = $this->database::$pdo->prepare($query);

        return $statement->execute($original);
    }

    public function delete(): bool
    {
        $data = $this->toArray();

        $query = "DELETE FROM $this->table WHERE id = :id";

        $statement = $this
            ->database
            ->getPDO()
            ->prepare($query);

        $statement->bindParam('id', $data['id']);

        return $statement->execute();
    }

    public function toArray(): array
    {
        return $this->original;
    }

    public function where(string $key, string $action, mixed $value)
    {
        $sql = "SELECT * FROM $this->table WHERE $key $action :value";

        $statement = $this->database
            ->getPDO()
            ->prepare($sql);

        $statement->bindParam(':value', $value);

        return $this;
    }

    public function get(){

    }

    public function getTable(): string
    {
        return $this->table;
    }
}