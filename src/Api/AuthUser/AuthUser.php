<?php
/**
 * User: Ruslan Yakupov ( ruslan.yakupov@tatar.ru )
 * Date: 08.09.2015
 */

namespace Api\AuthUser;


use Api\User\User;
use Base\Interfaces\ISingleton;
use Base\Traits\SingletonTrait;
use Services\Service;

/**
 * авторизованный пользователь
 * @property int $id
 */
class AuthUser implements ISingleton
{
    use SingletonTrait;

    /**
     * @var User
     */
    private static $curUser;

    private function __construct()
    {
        self::$curUser = new User();

        if (Service::request()->post('token')) {
            self::loadByToken(Service::request()->post('token'));
        }
    }

    /**
     * грузим по токену
     * @param $token
     * @return User
     * @throws \Exception
     */
    private static function loadByToken($token)
    {
        self::$curUser->loadByToken($token);
        if (!self::isAuth()) {
            throw new \Exception('Ошибка авторизации');
        } else {
            self::$curUser->updateToken($token);
        }
    }

    /**
     * Авторизован ли пользователь
     * @return null
     */
    public function isAuth()
    {
        return self::$curUser->id;
    }

    /**
     * @return User
     */
    public function current()
    {
        return self::$curUser;
    }

    /**
     * Авторизуем пользователя
     * @param $login
     * @param $password
     * @return null
     */
    public function login($login, $password)
    {
        self::$curUser->getByParam(['login' => $login, 'password' => $password]);
        if (self::$curUser->id) {
            return self::$curUser->updateToken(self::generateToken());
        }
        return null;
    }

    /**
     * генерим токен
     * @return string
     */
    private static function generateToken()
    {
        return base64_encode(md5(uniqid('', true), true));
    }
}