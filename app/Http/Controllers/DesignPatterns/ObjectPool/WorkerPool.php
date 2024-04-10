<?php

namespace App\Http\Controllers\DesignPatterns\ObjectPool;

use App\Http\Controllers\DesignPatterns\ObjectPool\ObjectPool\Worker;

class WorkerPool
{
    private array $occupiedWorkers = [];
    private array $freeWorkers = [];

    public function getWorker(): Worker
    {
        if (count($this->freeWorkers) == 0) {
            $id = count($this->occupiedWorkers) + count($this->freeWorkers) + 1;
            $worker = new Worker($id);
        } else {
            $worker = array_pop($this->freeWorkers);
        }
        $this->occupiedWorkers[$worker->getId()] = $worker;
        return $worker;
    }

    public function releaseWorker(Worker $worker): void
    {
        $id = $worker->getId();
        if (isset($this->occupiedWorkers[$id])) {
            unset($this->occupiedWorkers[$id]);
            $this->freeWorkers[$id] = $worker;
        }
    }
}

