<?php
/**
 * User: Ruslan Yakupov ( ruslan.yakupov@tatar.ru )
 * Date: 07.09.2015
 */

namespace Base\Request;

/**
 * Класс для работы с запросами
 * Class Request
 * @package Request
 */
class Request
{
    public function get($param = null, $default = null)
    {
        return self::formatReques($_GET, $param, $default);
    }

    private static function formatReques(array $requestType = [], $param, $default = null)
    {
        return $param ? isset($requestType[$param]) ? $requestType[$param] : $default : $requestType;
    }

    public function post($param = null, $default = null)
    {
        return self::formatReques($_POST, $param, $default);
    }
}