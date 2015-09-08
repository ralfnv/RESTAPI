<?php
/**
 * User: Ruslan Yakupov ( ralfnavi@gmail.com )
 * Date: 07.09.2015
 */

namespace Base\Config;

/**
 * Интрефейс конфига
 * Interface ConfigInterface
 * @package Base\Config
 */
interface ConfigInterface
{
    public function get($param = null);
}