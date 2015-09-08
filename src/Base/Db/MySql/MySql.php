<?php
/**
 * User: Ruslan Yakupov ( ralfnavi@gmail.com )
 * Date: 07.09.2015
 */

namespace Base\Db\MySql;


use Base\Db\Interfaces\DbInterface;
use Base\Interfaces\ISingleton;
use Base\Traits\SingletonTrait;
use PDO;

class MySql implements DbInterface, ISingleton
{
    use SingletonTrait;

    const SELECT = 'select';
    const UPDATE = 'update';
    const DELETE = 'delete';
    const INSERT = 'insert';
    /** @var  PDO */
    private $conn;

    private $isDebug;

    /**
     * @param mixed $isDebug
     */
    public function setIsDebug($isDebug)
    {
        $this->isDebug = $isDebug;
    }

    /**
     * Подключаемся к базе
     *
     * @param string $host
     * @param string $log
     * @param string $pass
     * @param string $bdname
     * @return PDO
     * @throws Exception
     */
    final public function connect($host, $log, $pass, $bdname)
    {

        if ($this->isConnect()) {
            throw new Exception('Подключение уже установлено');
        }
        try {
            $this->conn = new PDO('mysql:host=' . $host . ';dbname=' . $bdname . ';charset=utf8', $log, $pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $this->conn->query('SET character_set_database=utf8');
            $this->conn->query('SET character_set_client=utf8');
            $this->conn->query('SET NAMES "UTF8"');

            return $this;
        } catch (\PDOException $e) {
            throw new Exception("Подключение невозможно: " . $e->getMessage());
        }
    }

    public function isConnect()
    {
        return (bool)$this->conn;
    }

    /**
     * Запрос к БД
     * @param $query - запрос
     * @param array $binds - параметры
     * @return array
     * @throws Exception
     */
    public function select($query, array $binds = [])
    {
        return $this->queryBind($query, self::SELECT, $binds);
    }

    /**
     * Возвращает данные в зависимости от типа запроса
     * @param $query - запрос
     * @param $type - тип запроса
     * @param array $binds - массив параметров
     * @return array|int|string
     * @throws Exception
     */
    private function queryBind($query, $type, array $binds = [])
    {
        try {
            if (!$query) {
                throw new Exception('Пустой запрос.');
            }

            if (!$this->conn) {
                throw new Exception('Нет подключения к базе.');
            }

            /** @var $sqlRes \PDOStatement */
            $sqlRes = $this->conn->prepare($query);

            //биндим переменные
            if ($binds) {
                array_walk($binds, function ($value, $key) use ($sqlRes) {
                    $sqlRes->bindParam(":$key", $value, PDO::PARAM_STR);
                });
            }

            $sqlRes->execute();

            switch ($type) {
                case self::SELECT:
                    $Data = $sqlRes->fetchAll(PDO::FETCH_ASSOC);
                    break;
                case self::INSERT:
                    $Data = $this->conn->lastInsertId();
                    break;
                case self::UPDATE:
                case self::DELETE:
                    $Data = $sqlRes->rowCount();
                    break;
                default:
                    throw new Exception('Неизвестнй тип запроса');
            }

            $sqlRes->closeCursor();

            return $Data;
        } catch (\PDOException $e) {
            if($this->isDebug){
                throw new Exception(sprintf('В запросе: %s ошибка:  %s', $query, $e->getMessage()));
            } else {
                throw new Exception('Ошбика запроса');
            }

        }
    }

    /**
     * обновление данных
     * @param $query - запрос
     * @param array $binds - параметры
     * @return int
     * @throws Exception
     */
    public function update($query, array $binds = [])
    {
        return $this->queryBind($query, self::UPDATE, $binds);
    }

    /**
     * удаление данных
     * @param $query - запрос
     * @param array $binds - параметры
     * @return int
     * @throws Exception
     */
    public function delete($query, array $binds = [])
    {
        return $this->queryBind($query, self::DELETE, $binds);
    }

    /**
     * вставка данных
     * @param $query - запрос
     * @param array $binds - параметры
     * @return string
     * @throws Exception
     */
    public function insert($query, array $binds = [])
    {
        return $this->queryBind($query, self::INSERT, $binds);
    }

    final public function __destruct()
    {
        if (!$this->isConnect()) {
            $this->conn = null;
        }
    }
}