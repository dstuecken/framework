<?php

namespace DS\Component\Links;

/**
 * Dennis Stücken
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component\Time
 */
class HomeLink extends AbstractLink
{
    /**
     * @param string $path
     *
     * @return string
     */
    public static function get(string $path = '/'): string
    {
        return self::prepareUrl($path);
    }
}
