<?php
/**
 * User: Ruslan Yakupov ( ralfnavi@gmail.com )
 * Date: 07.09.2015
 */

namespace Base\Db\Interfaces;

/**
 * Интрефейс для работы с БД
 * Interface DbInterface
 * @package Db\Interfaces
 */
interface DbInterface
{
    /**
     * Запрос данных
     * @param $query - запрос
     * @param array $binds - параметры
     * @return array
     */
    public function select($query, array $binds = []);

    /**
     * Обновление данных
     * @param $query - запрос
     * @param array $binds - параметры
     * @return int
     */
    public function update($query, array $binds = []);

    /**
     * Удаление данных
     * @param $query - запрос
     * @param array $binds - параметры
     * @return int
     */
    public function delete($query, array $binds = []);

    /**
     * Вставка данных
     * @param $query - запрос
     * @param array $binds - параметры
     * @return string
     */
    public function insert($query, array $binds = []);
}