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
class ApiLink extends AbstractLink
{
    /**
     * @param string $path
     *
     * @return string
     */
    public static function get(string $path = '/', $version = 'v1'): string
    {
        return self::prepareUrl('/api/' . $version . '/' . $path);
    }
}
