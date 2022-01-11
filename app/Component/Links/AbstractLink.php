<?php

namespace DS\Component\Links;

/**
 * DS-Framework
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component\Time
 */
abstract class AbstractLink
{
    /**
     * @return \Phalcon\DiInterface
     */
    protected static function getDi()
    {
        return \Phalcon\Di::getDefault();
    }

    /**
     * @param string $path
     *
     * @return string
     */
    public static function prepareUrl(string $path = '/'): string
    {
        return sprintf('%s%s', self::getDi()->get('config')->get('url'), $path);
    }
}
