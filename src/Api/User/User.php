<?php
/**
 * User: Ruslan Yakupov ( ruslan.yakupov@tatar.ru )
 * Date: 07.09.2015
 */

namespace Api\User;

use Base\AbstaractModel\AbstaractModel;
use Services\Service;

/**
 * @property  string $name
 * @property string $password
 * @property  int $permission
 */
class User extends AbstaractModel
{
    const USER = 0;
    const ADMIN = 1;
    const SUPER_ADMIN = 2;
    protected static $TABLE = 'user';
    protected static $fields = [
        'id',
        'name',
        'age',
        'login',
        'password',
        'permission',
    ];

    protected $loadAfterSave = true;
    private $notNullFields = [
        'name' => 'Имя',
        'login' => 'Логин',
        'password' => 'Пароль',
        'permission' => 'Тип пользователя',
    ];

    /**
     * Пользователь админ
     * @return bool
     */
    public function isAdmin()
    {
        return $this->permission == self::ADMIN;
    }

    /**
     * Пользователь суперадмин
     * @return bool
     */
    public function isSuperAdmin()
    {
        return $this->permission == self::SUPER_ADMIN;
    }

    /**
     * Обычный пользователь
     * @return bool
     */
    public function isUser()
    {
        return $this->permission == self::USER;
    }

    /**
     * Верёнт массив доступных полей
     * @param array|null $fields
     * @return array
     */
    public function getDataArr(array $fields = null)
    {
        $data = [];
        $dataKey = $fields ? array_intersect(self::$fields, $fields) : self::$fields;

        foreach ($dataKey as $item) {
            $data[$item] = isset($this->{$item}) ? $this->{$item} : null;
        }
        return $data;
    }

    /**
     * Загрузить по условиям
     * @param array $paramData
     * @return $this
     * @throws \Base\Db\MySql\Exception
     */
    public function getByParam(array $paramData)
    {
        $cond = array_reduce(array_keys($paramData), function ($carry, $item) {
            $delimiter = '';
            if ($carry) {
                $delimiter = 'AND';
            }
            return "$carry $delimiter {$item} = :{$item}";
        });

        $data = Service::db()->select('SELECT ' . implode(',',
                static::$fields) . ' FROM ' . self::$TABLE . " WHERE {$cond} LIMIT 0,1", $paramData);
        if (isset($data[0])) {
            $this->assign($data[0]);
        }
        return $this;
    }


    /**
     * Пишем токен пользователя в базу
     * @param $token
     * @return null
     */
    public function updateToken($token)
    {
        if ($this->id) {
            $this->update(['token' => $token, 'tokenExpired' => date('Y-m-d H:i:s', strtotime('+1 hour'))]);
            return $token;
        } else {
            return null;
        }

    }

    /**
     * Грузим по токену
     * @param $token
     * @return null
     */
    public function loadByToken($token)
    {
        $data = Service::db()->select('SELECT ' . implode(',',
                static::$fields) . ' FROM ' . self::$TABLE . " WHERE token = :token AND UNIX_TIMESTAMP(tokenExpired) > UNIX_TIMESTAMP(:tokenExpired) LIMIT 0,1",
            ['token' => $token, 'tokenExpired' => date('Y-m-d H:i:s')]);
        if (isset($data[0])) {
            $this->assign($data[0]);
        }
        return $this;
    }

    protected function validField($key, $value)
    {
        if (array_key_exists($key, $this->notNullFields) && in_array($value, [null, ''], true)) {
            throw new \Exception("Не заполнено поле " . $this->notNullFields[$key]);
        }
        return true;
    }
}