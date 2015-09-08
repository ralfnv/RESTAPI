<?php
/**
 * User: Ruslan Yakupov ( ralfnavi@gmail.com )
 * Date: 07.09.2015
 */

namespace Base\Config;

use Base\Interfaces\ISingleton;
use Base\Traits\SingletonTrait;

/**
 * Файл конфига
 * Class Config
 * @package Base\Config
 */
class Config implements ISingleton, ConfigInterface
{
    use SingletonTrait;
    /**
     * распарсенный конфиг
     * @var array
     */
    private static $cfg;

    /**
     * Инициализация конфига
     * @throws Exception
     */
    private function __construct()
    {
        $cfgFile = file_get_contents("{$_SERVER['DOCUMENT_ROOT']}/config.json");
        if (!$cfgFile) {
            throw new Exception("Конфигурационный файл не найден");
        }
        $decode = json_decode($cfgFile, true);
        if ($decode) {
            self::$cfg = $decode;
        } else {
            throw new Exception(sprintf("Конфигурационный файл не корректен %s: %s", json_last_error_msg(),
                json_last_error()));
        }
    }

    /**
     * Получить секцию конфига
     * @param null $param
     * @return array|mixed
     * @throws Exception
     */
    public function get($param = null)
    {
        if ($param) {
            return isset(self::$cfg[$param]) ? self::$cfg[$param] : null;
        } else {
            return self::$cfg;
        }
    }
}