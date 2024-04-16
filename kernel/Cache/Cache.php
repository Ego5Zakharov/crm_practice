<?php

namespace App\Kernel\Cache;

class Cache
{
    protected string $cacheSavePath = APP_PATH . "/cache";

    // разделитель при кэшировании - key . $separator . unique_id() . mt_rand()
    protected string $separator = "%%%%";

    public function set(string $key, mixed $data)
    {
        $cachePath = $this->getCacheSavePath();

        if (!file_exists($cachePath)) {
            mkdir($cachePath, 0777, true);
        }

        $fileName = "$key" . $this->getSeparator() . uniqid("", true) . mt_rand(1, 999) . ".txt";

        // создаем файл на запись, если не существует
        //        $fileAlreadyHas = substr($fileName, 0, strlen($key));
        //
        //        dd($fileAlreadyHas);

        //        if (file_exists()) {
        //
        //        }

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


//$decodedData = base64_decode($data);
//$unserializedData = unserialize($decodedData);
//dd($unserializedData);

    public function get()
    {

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