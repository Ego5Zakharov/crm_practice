<?php

namespace App\Kernel\Database;

use PDO;
use PDOException;

abstract class Model
{
    // первичный ключ
    protected string $primaryKey = "id";

    // подключение к бд
    protected Database $database;

    // оригинальные данные записи подлежащие заполнению
    protected array $original = [];

    // текущие данные записи(любые)
    protected array $current = [];

    // переменные которые подлежат заполнению в таблице
    protected array $fillable = [];

    // название таблицы
    protected string $table = "";

    // разрешено ли массовое заполнение
    protected bool $guard = true;

    // связи модели
    protected array $relations = [];

    // какие связи модели подгружать сразу
    protected array $with = [];

    public function __construct(array|Model $data = [])
    {
        $this->database = Database::getInstance();

        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public function __get(string $value)
    {
        if (array_key_exists($value, $this->original)) {
            return $this->original[$value];
        } else if (array_key_exists($value, $this->current)) {
            return $this->current[$value];
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

        $this->current[$key] = $value;
    }

    public function toArray(): array
    {
        return $this->original;
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
        $this->current = $model->current;

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

    /**
     * путь до модели с которой у нас связь
     * @param string|Model $relatedModelPath
     *
     * ключ который связывает эти модели
     * @param string $foreignId
     *
     * первичный ключ основной таблицы из которой реализуется связь
     * @param string $originalId
     * @return mixed
     */
    public function hasOne(string|Model $relatedModelPath, string $foreignId, string $originalId): mixed
    {
        $relatedModel = new $relatedModelPath;
        $relatedModelTable = $relatedModel->table;

        if (!$this->original[$foreignId]) {
            throw new PDOException("Incorrect foreignId value!");
        }
        $originalIdValue = $this->original[$foreignId];

        $query = "SELECT * FROM $relatedModelTable WHERE $originalId = :$originalId";

        $statement = $this->database->getPDO()->prepare($query);

        $statement->bindParam(":$originalId", $originalIdValue);

        $statement->execute();

        return $statement->fetch(PDO::FETCH_ASSOC);
    }

}