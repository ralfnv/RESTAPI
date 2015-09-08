<?php
/**
 * User: Ruslan Yakupov ( ralfnavi@gmail.com )
 * Date: 07.09.2015
 */

namespace Base\Traits;


trait SingletonTrait
{
    private static $instance;

    private function __construct()
    {
    }

    /**
     * @return static
     */
    public static function getInstance()
    {
        if (static::$instance === null) {
            static::$instance = new static;
        }

        return static::$instance;
    }
}