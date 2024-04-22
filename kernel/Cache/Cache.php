<?php

namespace App\Kernel\Cache;

class Cache
{
    protected string $cacheSavePath = APP_PATH . "/cache";

    // разделитель при кэшировании - key . $separator . unique_id() . mt_rand()
    protected string $separator = "%%%%";


    /**
     * @param string $key
     * @param mixed $data
     * @return bool
     *
     * Сохраняет данные в кэше в файл
     */
    public function set(string $key, mixed $data)
    {
        $cachePath = $this->getCacheSavePath();

        if (!file_exists($cachePath)) {
            mkdir($cachePath, 0777, true);
        }

        $fileName = "$key" . $this->getSeparator() . uniqid("", true) . mt_rand(1, 999) . ".txt";

        $cacheFiles = scandir($this->getCacheSavePath());
        // новый массив без ['.', '..'] файлов
        $cacheFiles = array_diff($cacheFiles, ['.', '..']);

        foreach ($cacheFiles as $file) {
            // если файл в папке имеет уже текущий ключ
            // и мы пытаемся записать под этим ключом новые данные - перезаписываем и удаляем прошлые данные связанные с этим ключом
            // удаляем старый файл
            if (
                substr($file, 0, strlen($key)) == substr($fileName, 0, strlen($key))
            ) {
                unlink($this->cacheSavePath . "/" . $file);
            }
        }

        $file = fopen("$cachePath/$fileName", 'w');

        if ($file) {
            // сериализуем данные, для того, чтобы закодировать строку
            $serialized64Data = base64_encode(serialize($data));

            $bytesWritten = fwrite($file, $serialized64Data);

            // все записалось
            if ($bytesWritten !== false) {
                fclose($file);
                return true;
            } // возникли проблемы
            else {
                fclose($file);
                return false;
            }
            // если файл с ключом $key уже существует в папке кэширования - перезаписать
        } else {
            return false;
        }

    }


    /**
     * @param string $key
     * @return mixed
     * @throws NotFoundCacheSavePatchException
     *
     * Выдает данные по ключу, если не находит - false
     *
     */
    public function get(string $key): mixed
    {
        if (!file_exists($this->getCacheSavePath())) {
            throw new NotFoundCacheSavePatchException("Not found cache patch. You need create or recreate it");
        }

        $cacheFiles = array_diff(scandir($this->getCacheSavePath()), ['.', '..']);

        foreach ($cacheFiles as $cacheFileName) {
            // ищем это слово
            $subStr = substr($cacheFileName, 0, strlen($key));
            // если слово === ключу - обрабатываем
            if ($subStr === $key) {
                $fileHandle = fopen($this->getCacheSavePath() . "/$cacheFileName", 'r');
                // если удалось открыть файл - обрабатываем
                if ($fileHandle) {
                    $fileContent = fread($fileHandle, filesize($this->getCacheSavePath() . "/$cacheFileName"));
                    fclose($fileHandle);
                    // расшифровываем и десериализуем файл
                    return unserialize(base64_decode($fileContent));
                }
            }
        }
        return false;
    }

    public function setCacheSavePath(string $cacheSavePath): void
    {
        $this->cacheSavePath = $cacheSavePath;
    }

    public function getCacheSavePath(): string
    {
        return $this->cacheSavePath;
    }

    public function getSeparator(): string
    {
        return $this->separator;
    }

    public function setSeparator(string $separator): void
    {
        $this->separator = $separator;
    }


}