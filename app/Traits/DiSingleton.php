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
trait DiSingleton
{
    // Get di injecttion functions as well as __wakup and __clone from Singelton
    use DiInjection, Singleton;
    
    /**
     * @var Singleton
     */
    protected static $instance = NULL;
    
    /**
     * Return singleton instance of current class
     *
     * @param \Phalcon\Di\DiInterface $dependencyInjector
     *
     * @return static
     */
    public static function instance(\Phalcon\Di\DiInterface $dependencyInjector)
    {
        $l_class = get_called_class();
        if (!isset(static::$instance[$l_class]) || is_null(static::$instance[$l_class]))
        {
            static::$instance[$l_class] = new $l_class($dependencyInjector);
        }

        return static::$instance[$l_class];
    }
}
