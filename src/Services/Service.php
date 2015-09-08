<?php
namespace Services;

use Api\AuthUser\AuthUser;
use Api\User\User;
use Base\Config\Config;
use Base\Db\MySql\Exception;
use Base\Db\MySql\MySql;
use Base\Request\Request;

/**
 * User: Ruslan Yakupov ( ralfnavi@gmail.com )
 * Date: 07.09.2015
 */
final class Service
{
    private function __construct()
    {
    }

    /**
     * Работа с бд
     * @return \Base\Db\Interfaces\DbInterface;
     * @throws Exception
     */
    public static function db()
    {
        try {
            $sql = MySql::getInstance();
            $sql->setIsDebug((bool)self::config()->get('isDebug'));
            $cfg = self::config()->get('db');
            return $sql->isConnect() ? $sql : $sql->connect($cfg['host'], $cfg['user'], $cfg['passw'], $cfg['name']);
        } catch (\Exception $e) {
            throw new Exception('Ошибка подключения');
        }
    }

    /**
     * Работа с конфигом
     * @return \Base\Config\ConfigInterface;
     */
    public static function config()
    {
        return Config::getInstance();
    }

    public static function request()
    {
        return new Request();
    }

    /**
     * @return User
     */
    public static function user()
    {
        return AuthUser::getInstance()->current();
    }
}