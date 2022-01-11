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
class Text
{
    /**
     * @param $string
     * @param $delimiter
     *
     * @return mixed|string
     */
    public static function clean($string, $delimiter)
    {
        // replace non letter or non digits by -
        //$string = preg_replace('#[^\pL\d]+#u', '-', $string);
        $string = str_replace(' ', $delimiter, $string);

        // Trim trailing $delimiter
        $string = trim($string, $delimiter);

        $clean = preg_replace('~[^-\w]+~', '', $string);
        //$clean = strtolower($clean);
        $clean = preg_replace('#[\/|+ -]+#', $delimiter, $clean);
        $clean = trim($clean, $delimiter);

        return $clean;
    }
    
    /**
     * @param string $string
     * @param int    $length
     * @param string $append
     *
     * @return string
     */
    public static function substr(string $string, int $length, $append = '..'): string
    {
        if (strlen($string) > $length)
        {
            return substr($string, 0, $length) . $append;
        }
        
        return $string;
    }
}
