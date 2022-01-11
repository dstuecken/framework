<?php
namespace DS\Traits;

/**
 * DS-Framework
 *
 * Singleton trait. Just do "use Singleton;" and you're ready to go.
 *
 * @author Dennis Stücken
 * @license proprietary

 * @copyright Dennis Stücken
 * @link https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component\Provider
 */
trait Singleton
{
    /**
     * @var Singleton
     */
    protected static $instance = NULL;

    /**
     * Return singleton instance of current class
     *
     * @return static
     */
    public static function instance(/* $param1, $param2, ... */)
    {
        $l_class = get_called_class();
        if (!isset(static::$instance[$l_class]) || is_null(static::$instance[$l_class]))
        {
            static::$instance[$l_class] = new $l_class;
        }

        return static::$instance[$l_class];
    }

    /** @noinspection PhpUnusedPrivateMethodInspection */
    final private function __wakeup()
    {
    }

    /**
     * Prevent cloning
     */
    final private function __clone()
    {
    }
}
