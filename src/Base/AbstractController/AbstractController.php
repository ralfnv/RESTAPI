<?php
/**
 * User: Ruslan Yakupov ( ruslan.yakupov@tatar.ru )
 * Date: 08.09.2015
 */

namespace Base\AbstractController;

abstract class AbstractController
{
    protected $beforeActions = [];
    private $action;


    /**
     * @param $action
     * @return mixed
     * @throws \Exception
     */
    public function run($action)
    {
        $this->action = $action;
        $this->beforeAction();


        $actionName = $this->action . 'Action';
        if (method_exists($this, $actionName)) {
            return call_user_func([$this, $actionName]);
        } else {
            throw new \Exception('Событие не найдено');
        }
    }

    /**
     * Выполняется до выполнения всех action
     */
    private function beforeAction()
    {

        $actions = isset($this->beforeActions[$this->action]) ? $this->beforeActions[$this->action] : null;

        if ($actions) {
            foreach ($actions as $method) {
                if (method_exists($this, $method)) {
                    call_user_func([$this, $method]);
                } else {
                    throw new \Exception("Не найден метод {$method} класса " . static::class);
                }
            }
        }
    }

    /**
     * @return mixed
     */
    public function getAction()
    {
        return $this->action;
    }
}