<?php
namespace Base\AutoLoader;
/**
 * User: Ruslan Yakupov ( ralfnavi@gmail.com )
 * Date: 07.09.2015
 */
class AutoLoader
{
    public static function init()
    {
        spl_autoload_register([AutoLoader::class, 'autoload']);
    }

    /**
     * @param $className
     * @return mixed
     * @throws Exception
     */
    public static function autoload($className)
    {
        $normalPath = str_replace('\\', '/', $className) . '.php';
        $file = $_SERVER['DOCUMENT_ROOT'] . '/src/' . $normalPath;
        if (file_exists($file)) {
            require_once $file;
        } else {
            throw new Exception('Класс  ' . $className . ' не найден.');
        }
    }
}