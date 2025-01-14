<?php

namespace App\Kernel\Database\Concerns;

use App\Kernel\Database\Model;
use PDO;

trait HasRelationships
{
    // связи модели
    public array $relations = [];

    /**
     * путь до модели с которой у нас связь
     * @param string $relatedModelPath
     *
     * ключ который связывает эти модели
     * @param string $foreignId
     *
     * первичный ключ основной таблицы из которой реализуется связь
     * @param string $originalId
     * @return mixed
     */
    public function hasOne(string $relatedModelPath, string $foreignId, string $originalId): mixed
    {
        $relatedModel = new $relatedModelPath;
        $relatedModelTable = $relatedModel->table;

        if (!key_exists($foreignId, $this->getOriginals())) {
            return null;
        }

        $originalIdValue = $this->original[$foreignId];

        $query = "SELECT * FROM $relatedModelTable WHERE $originalId = :$originalId";

        $statement = $this->database->getPDO()->prepare($query);

        $statement->bindParam(":$originalId", $originalIdValue);

        $model = $statement->execute() ? $statement->fetch(PDO::FETCH_ASSOC) : null;

        if (!$model) {
            return null;
        }
        return new $relatedModel($model);
    }

    public function belongsTo(string $relatedModelPath, string $foreignId, string $localId)
    {
        // возвращает запись на ссылающуюся таблицу

        $model = new $relatedModelPath();

        $foreignIdValue = $this->$foreignId;

        if (!$foreignIdValue) {
            return null;
        }

        $this->builder->setQuery("SELECT * FROM {$model->getTable()} WHERE $localId = :$foreignId");

        $this->builder->prepareQuery();

        $this->builder->bindParam($foreignId, $foreignIdValue);

        $fetchResult = $this->builder->execute()->fetch();

        if (!$fetchResult) {
            return null;
        }
        return new $relatedModelPath($fetchResult);
    }

    public function hasMany(
        string $relatedModelPath,
        string $foreignId,
        string $localId
    ): ?array
    {
        $relatedModel = new $relatedModelPath();

        $relatedModelTable = $relatedModel->table;

        if (!key_exists($localId, $this->getOriginals())) {
            return null;
        }

        $localIdValue = $this->id;

        $query = "SELECT * FROM $relatedModelTable WHERE $foreignId = :$localId";

        $statement = $this->database->getPDO()->prepare($query);

        $statement->bindParam(':id', $localIdValue);

        $data = $statement->execute() ? $statement->fetchAll(PDO::FETCH_ASSOC) : null;

        if (!$data) {
            return null;
        }

        $models = [];

        foreach ($data as $item) {
            $model = new $relatedModelPath($item);
            $models[] = $model;
        }

        return $models ?? null;
    }

    public function belongsToMany(
        string $relatedModelPath,
        string $commonTable,
        string $foreignId,
        string $secondForeignId): ?array
    {
        $relatedModel = new $relatedModelPath();

        $localTable = $this->table;
        $relatedTable = $relatedModel->table;

        $localForeignIdValue = $this->id;

        $query = "SELECT * FROM $commonTable 
        INNER JOIN $localTable ON $commonTable.$foreignId = $localTable.id
        INNER JOIN $relatedTable ON $commonTable.$secondForeignId = $relatedTable.id
        WHERE $localTable.id = :localForeignIdValue";

        $statement = $this->database->getPDO()->prepare($query);

        $statement->bindParam('localForeignIdValue', $localForeignIdValue);

        $data = $statement->execute() ? $statement->fetchAll(PDO::FETCH_ASSOC) : null;

        if (!$data) {
            return null;
        }

        $relatedModelKeys = array_values($relatedModel->fillable);

        $relatedModelData = [];

        $relatedModels = [];

        foreach ($data as $iterator => $item) {
            $relatedModelData[$iterator] = [];

            foreach ($relatedModelKeys as $key) {

                if (array_key_exists($key, $item)) {
                    $relatedModelData[$iterator][$key] = $item[$key];
                }

            }
        }

        foreach ($relatedModelData as $data) {
            $relatedModels[] = new $relatedModelPath($data);
        }

        return $relatedModels;
    }

    public function setWithRelation(string $key, mixed $value): void
    {
        $this->with[$key] = $value;
    }

    public function getRelations(): array
    {
        return $this->relations;
    }

    public function getRelation(string $key)
    {
        return $this->relations[$key];
    }

    public function setRelation(string $key, mixed $value): void
    {
        $this->relations[$key] = $value;
    }

    public function setRelations(array $relations = []): void
    {
        $this->relations = $relations;
    }
}