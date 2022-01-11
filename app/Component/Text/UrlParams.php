<?php

namespace DS\Component\Text;

/**
 * DS-Framework
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component\Text
 */
class UrlParams
{
    /**
     * Add parameter like ref to a url
     *
     * @param string $url
     * @param string $varName
     * @param string $value
     *
     * @return string
     */
    public static function add(string $url, string $varName, string $value = ''): string
    {
        if ($value)
        {
            $attach = $varName . "=" . $value;
        }
        else
        {
            $attach = $varName;
        }

        // is there already a ?
        if (strpos($url, "?"))
        {
            $url .= "&" . $attach;
        }
        else
        {
            $url .= "?" . $attach;
        }

        return $url;
    }
}
