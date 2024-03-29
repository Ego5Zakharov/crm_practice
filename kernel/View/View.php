<?php


namespace App\Kernel\View;

class View
{
    /**
     * @throws ViewNotFoundException
     */
    public function view(string $filePath, array $data = []): mixed
    {
        $filePath = base_path() . "/views/pages/$filePath.php";

        if (!file_exists($filePath)) {
            throw new ViewNotFoundException("$filePath not found.");
        }

        extract($data);

        return include_once $filePath;
    }
    
    /**
     * @throws ViewComponentNotFoundException
     */
    public function component(string $path, array $data = []): bool|string
    {
        $filePath = base_path() . "/views/components/$path.php";

        if (!file_exists($filePath)) {
            throw new ViewComponentNotFoundException("$filePath not found.");
        }

        extract($data);

        return file_get_contents($filePath);
    }
}