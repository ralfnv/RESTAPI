<?php
/**
 * User: Ruslan Yakupov ( ruslan.yakupov@tatar.ru )
 * Date: 07.09.2015
 */

namespace Base\AbstaractModel;

use Services\Service;

abstract class AbstaractModel
{
    /**
     * массив полей доступных для записи в бд
     * @var array
     */
    protected static $fields = [];
    protected static $TABLE;
    public $id = null;

    /**
     * Загружать после сохранения
     * @var bool
     */
    protected $loadAfterSave = false;

    public function __construct($initData = null)
    {
        $data = null;
        if (is_numeric($initData)) {
            $this->load($initData);
        } elseif (is_array($initData)) {
            $this->assign($initData);
        }
    }

    public function load($id)
    {
        $data = Service::db()->select(sprintf('SELECT %s FROM %s WHERE id = :id', implode(',', static::$fields),
            static::$TABLE), ['id' => $id]);
        if (isset($data[0])) {
            $this->assign($data[0]);
        } else {
            throw new \Exception('Запись не найдена');
        };
        return $this;
    }

    /**
     * Устанавливаем значения
     * @param array $data
     * @return $this
     */
    public function assign(array $data)
    {
        array_walk($data, function ($value, $key) {
            $this->{$key} = $value;
        });
        return $this;
    }

    /**
     * @param array|null $fields - спиcок полей, которые необходимо сохранить, если null сохраняем все поля
     * @return @inheritdoc
     * @throws \Exception
     */
    public function save(array $fields = null)
    {
        $data = $this->getData($fields);
        if ($this->isNewRecord()) {
            $this->id = $this->insert($data);
        } else {
            $this->update($data);
        }

        if ($this->loadAfterSave) {
            $this->load($this->id);
        }

        return $this;
    }

    /**
     * Массив переменных для сохранения
     * @param array $fields
     * @return array
     */
    private function getData(array $fields = null)
    {

        $data = [];
        $dataKey = $fields ? array_intersect(static::$fields, $fields) : static::$fields;
        foreach ($dataKey as $item) {
            if ($item === 'id') {
                continue;
            }

            $value = isset($this->{$item}) ? $this->{$item} : null;

            if ($this->validField($item, $value)) {
                $data[$item] = $value;
            }
        }
        return $data;
    }

    abstract protected function validField($key, $value);

    /**
     * новая запись
     * @return bool
     */
    private function isNewRecord()
    {
        return !$this->id;
    }

    /**
     * Вставляем данные
     * @param array $data
     * @return string
     * @throws \Base\Db\MySql\Exception
     */
    protected function insert(array $data)
    {
        $fields = array_keys($data);
        return Service::db()->insert(sprintf("INSERT INTO %s (%s) VALUES(:%s)", static::$TABLE,
            implode(', ', $fields),
            implode(', :', $fields)), $data);
    }

    /**
     * Обновляем данные
     * @param array $data
     * @return int
     * @throws \Base\Db\MySql\Exception
     */
    protected function update(array $data)
    {
        $fieldsCondArr = [];
        $data['id'] = $this->id;
        foreach ($data as $key => $value) {
            $fieldsCondArr[] = "{$key} = :{$key}";
        }

        Service::db()->update(sprintf("UPDATE %s SET %s WHERE id = :id ", static::$TABLE,
            implode(', ', $fieldsCondArr)), $data);
    }

    /**
     * Удалить запись
     * @return int
     * @throws \Base\Db\MySql\Exception
     */
    public function delete()
    {
        return Service::db()->delete('DELETE FROM ' . static::$TABLE . ' WHERE id = :id', ['id' => $this->id]);
    }

    /**
     * Выставляем свойства модели
     * @param $name
     * @param $value
     * @throws \Exception
     */
    public function __set($name, $value)
    {
        if (in_array($name, static::$fields)) {
            $this->{$name} = $value;
        } else {
            throw new \Exception("Поле {$name} отсутсвует");
        }
    }

    /**
     *
     * @param $state
     * @param $data
     * @return array
     */
    protected static function result($state, $data)
    {
        return ['state' => (bool)$state, 'data' => $data];
    }
}