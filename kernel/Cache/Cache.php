<?php

namespace App\Kernel\Cache;

class Cache
{
    protected string $cacheSavePath = APP_PATH . "/cache";

    // разделитель при кэшировании - key . $separator . unique_id() . mt_rand()
    protected string $separator = "%%%%";

    protected static ?Cache $instance = null;


    private function __construct()
    {
    }

    /**
     * @param string $key
     * @param mixed $data
     * @return bool
     *
     * Сохраняет данные в кэше в файл
     *
     */
    public static function set(string $key, mixed $data): bool
    {
        self::initialize();

        $cachePath = self::$instance->getCacheSavePath();

        if (!file_exists($cachePath)) {
            mkdir($cachePath, 0777, true);
        }

        $fileName = "$key" . self::$instance->getSeparator() . uniqid("", true) . mt_rand(1, 999) . ".txt";

        $cacheFiles = scandir(self::$instance->getCacheSavePath());
        // новый массив без ['.', '..'] файлов
        $cacheFiles = array_diff($cacheFiles, ['.', '..']);

        foreach ($cacheFiles as $file) {
            // если файл в папке имеет уже текущий ключ
            // и мы пытаемся записать под этим ключом новые данные - перезаписываем и удаляем прошлые данные связанные с этим ключом
            // удаляем старый файл
            if (
                substr($file, 0, strlen($key)) == substr($fileName, 0, strlen($key))
            ) {
                unlink(self::$instance->cacheSavePath . "/" . $file);
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
    public static function get(string $key): mixed
    {
        self::initialize();

        if (!file_exists(self::$instance->getCacheSavePath())) {
            throw new NotFoundCacheSavePatchException("Not found cache patch. You need create or recreate it");
        }

        $cacheFiles = array_diff(scandir(self::$instance->getCacheSavePath()), ['.', '..']);

        foreach ($cacheFiles as $cacheFileName) {
            // ищем это слово
            $subStr = substr($cacheFileName, 0, strlen($key));
            // если слово === ключу - обрабатываем
            if ($subStr === $key) {
                $fileHandle = fopen(self::$instance->getCacheSavePath() . "/$cacheFileName", 'r');
                // если удалось открыть файл - обрабатываем
                if ($fileHandle) {

                    // удаляем файл, если срок его жизни истек
                    // filectime возвращает время, прошедшее с создания файла
                    $fileCreationTime = filectime(self::$instance->getCacheSavePath() . "/$cacheFileName");
                    // текущее время
                    $currentTime = time();
                    // сколько секунд прошло с момента создания
                    $timeElapsed = $currentTime - $fileCreationTime;
                    // если время хранения кэша вышло - удаляем файл
                    if ($timeElapsed >= config('cache.timeout')) {
                        unlink(self::$instance->getCacheSavePath() . "/$cacheFileName");
                        return false;
                    }

                    $fileContent = fread($fileHandle, filesize(self::$instance->getCacheSavePath() . "/$cacheFileName"));
                    fclose($fileHandle);
                    // расшифровываем и десериализуем файл
                    return unserialize(base64_decode($fileContent));
                }
            }
        }
        return false;
    }

    /**
     * @throws NotFoundCacheSavePatchException
     */
    public static function exists(string $key)
    {
        $result = self::get($key);
        /**
         * Если значение не найдено - вернуть null,
         * иначе - вернуть значение
         */
        return $result === false ? false : $result;
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

    public static function getInstance(): ?Cache
    {
        return self::$instance;
    }

    public static function initialize(): void
    {
        if (self::$instance === null) {
            self::$instance = new Cache();
        }
    }
}