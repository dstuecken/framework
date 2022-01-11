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
class Name
{
    /**
     * Extract firstname and lastname from string. Middle names are added to firstname.
     *
     * @param string $string
     *
     * @return array
     */
    public static function extractFirstLastName($string): array
    {
        $fullname = trim($string);
        if ($fullname)
        {
            $firstName = trim(substr($fullname, 0, strpos($fullname, ' ')));
            $lastName  = trim(substr($fullname, strpos($fullname, ' '), strlen($fullname)));

            return [
                $firstName,
                $lastName,
            ];
        }

        return ['', ''];
    }
}
