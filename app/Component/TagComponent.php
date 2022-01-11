<?php
namespace DS\Component;

use Phalcon\Tag;

/**
 * DS-Framework
 *
 * @author Dennis Stücken
 * @license proprietary

 * @copyright Dennis Stücken
 * @link https://www.dvlpr.de
 *
 * @version $Version$
 * @package DS\Component
 */
class TagComponent extends Tag
{
    /**
     * @var TagComponent
     */
    private static $instance = null;

    /**
     * @return TagComponent
     */
    public static function instance()
    {
        if (!self::$instance)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }
}
