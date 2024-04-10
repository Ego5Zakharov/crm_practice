<?php

namespace App\Http\Controllers\DesignPatterns\ObjectPool\ObjectPool;

use App\Http\Controllers\DesignPatterns\ObjectPool\WorkerPool;

class ObjectPoolController
{
    public function handle(): void
    {
        $pool = new WorkerPool();
        $worker1 = $pool->getWorker();
        $worker2 = $pool->getWorker();

        $pool->releaseWorker($worker1);
        $worker3 = $pool->getWorker();

        echo $worker3->getId(); // Выведет номер работника, освободившегося и занятого повторно
    }
}
