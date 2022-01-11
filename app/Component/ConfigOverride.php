<?php

namespace DS\Component;

use DS\Application;

/**
 * DS-Framework
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component
 */
class ConfigOverride
{

    /**
     * @param string $key
     * @param        $value
     */
    public static function override(string $key, $value)
    {
        Application::instance()->getConfig()->offsetSet($key, $value);
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public static function getKey(string $key)
    {
        return Application::instance()->getConfig()[$key];
    }

}
