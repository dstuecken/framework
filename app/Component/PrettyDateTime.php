<?php
namespace DS\Component;

use DS\Application;

/**
 * DS-Framework
 *
 * PrettyDateTime: Base Code taken from "danielstjules/php-pretty-datetime"
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component
 */
class PrettyDateTime
{
    // The constants correspond to units of time in seconds
    const MINUTE = 60;
    const HOUR = 3600;
    const DAY = 86400;
    const WEEK = 604800;
    const MONTH = 2628000;
    const YEAR = 31536000;

    /**
     * A helper used by parse() to create the human readable strings. Given a
     * positive difference, corresponding to a date in the past, it appends the
     * word 'ago'. And given a negative difference, corresponding to a date in
     * the future, it prepends the word 'In'. Also makes the unit of time plural
     * if necessary.
     *
     * @param  integer $difference The difference between dates in any unit
     * @param  string  $unit       The unit of time
     *
     * @return string  The date in human readable format
     */
    private static function prettyFormat($difference, $unit)
    {
        // $prepend is added to the start of the string if the supplied
        // difference is greater than 0, and $append if less than
        $prepend = ($difference < 0) ? 'In ' : '';
        $append  = ($difference > 0) ? ' ago' : '';

        $difference = floor(abs($difference));

        // If difference is plural, add an 's' to $unit
        if ($difference > 1)
        {
            $unit = $unit . 's';
        }

        return sprintf('%s%d %s%s', $prepend, $difference, $unit, $append);
        //return sprintf(
        //    application()->getDI()->get('intl')->t($prepend . '%d ' . $unit . $append), $difference
        //);
    }

    /**
     * Returns a pretty, or human readable string corresponding to the supplied
     * $dateTime. If an optional secondary DateTime object is provided, it is
     * used as the reference - otherwise the current time and date is used.
     *
     * Examples: 'Moments ago', 'Yesterday', 'In 2 years'
     *
     * @param  \DateTime $dateTime  The DateTime to parse
     * @param  \DateTime $reference (Optional) Defaults to the DateTime('now')
     *
     * @return string The date in human readable format
     * @throws \Exception
     */
    public static function day(\DateTime $dateTime, \DateTime $reference = null, $daysOnly = false)
    {
        // If not provided, set $reference to the current DateTime
        if (!$reference)
        {
            $reference = new \DateTime(null, new \DateTimeZone($dateTime->getTimezone()->getName()));
        }

        // Get the date corresponding to the $dateTime
        $date = $dateTime->format('Y/m/d');

        // Today
        if ($reference->format('Y/m/d') == $date)
        {
            return 'Today';
        }

        $yesterday = clone $reference;
        $yesterday->modify('- 1 day');

        if ($yesterday->format('Y/m/d') == $date)
        {
            return 'Yesterday';
        }
        else
        {
            $yesterday2 = clone $reference;
            $yesterday2->modify('- 2 day');

            if ($yesterday2->format('Y/m/d') == $date)
            {
                return 'Day before yesterday';
            }
            else
            {
                $tomorrow = clone $reference;
                $tomorrow->modify('+ 1 day');

                $tomorrow2 = clone $reference;
                $tomorrow2->modify('+ 2 day');

                if ($tomorrow->format('Y/m/d') == $date)
                {
                    return 'Tomorrow';
                }
                else if ($tomorrow2->format('Y/m/d') == $date)
                {
                    return 'Day after tomorrow';
                }
            }
        }

        if ($daysOnly)
        {
            // Get the difference between the current date and the supplied $dateTime
            $difference = $reference->format('U') - $dateTime->format('U');

            // Throw exception if the difference is NaN
            if (is_nan($difference))
            {
                throw new \Exception('The difference between the DateTimes is NaN.');
            }

            if ($difference > 0)
            {
                return number_format($difference / 86400) . ' days ago';
            }
            else
            {
                return 'in ' . number_format($difference / 86400) . ' days';
            }
        }
        else
        {
            return self::parse($dateTime, $reference);
        }
    }

    /**
     * Returns a pretty, or human readable string corresponding to the supplied
     * $dateTime. If an optional secondary DateTime object is provided, it is
     * used as the reference - otherwise the current time and date is used.
     *
     * Examples: 'Moments ago', 'Yesterday', 'In 2 years'
     *
     * @param  \DateTime $dateTime  The DateTime to parse
     * @param  \DateTime $reference (Optional) Defaults to the DateTime('now')
     *
     * @return string The date in human readable format
     * @throws \Exception
     */
    public static function parse(\DateTime $dateTime, \DateTime $reference = null)
    {
        // If not provided, set $reference to the current DateTime
        if (!$reference)
        {
            $reference = new \DateTime(null, new \DateTimeZone($dateTime->getTimezone()->getName()));
        }

        // Get the difference between the current date and the supplied $dateTime
        $difference = $reference->format('U') - $dateTime->format('U');
        $absDiff    = abs($difference);

        // Get the date corresponding to the $dateTime
        $date = $dateTime->format('Y/m/d');

        // Throw exception if the difference is NaN
        if (is_nan($difference))
        {
            throw new \Exception('The difference between the DateTimes is NaN.');
        }

        // Today
        if ($reference->format('Y/m/d') == $date)
        {
            if (0 <= $difference && $absDiff < self::MINUTE)
            {
                return 'Moments ago';
            }
            elseif ($difference < 0 && $absDiff < self::MINUTE)
            {
                return 'Seconds from now';
            }
            elseif ($absDiff < self::HOUR)
            {
                return self::prettyFormat($difference / self::MINUTE, 'min');
            }
            else
            {
                return self::prettyFormat($difference / self::HOUR, 'h');
            }
        }

        // Not Today
        $yesterday = clone $reference;
        $yesterday->modify('- 1 day');

        $tomorrow = clone $reference;
        $tomorrow->modify('+ 1 day');

        // Showing hrs ago if it happened less then 12 hours ago
        if ($absDiff / self::HOUR <= 12)
        {
            return self::prettyFormat($difference / self::HOUR, 'h');
        }
        // Showing yesterday if its over 12 hours
        if ($yesterday->format('Y/m/d') == $date)
        {
            return 'Yesterday';
        }
        // w00t It's Tomorrow
        else if ($tomorrow->format('Y/m/d') == $date)
        {
            return 'Tomorrow';
        }
        else if ($absDiff / self::DAY <= 7)
        {
            return self::prettyFormat($difference / self::DAY, 'day');
        }
        else if ($absDiff / self::WEEK <= 5)
        {
            return self::prettyFormat($difference / self::WEEK, 'week');
        }
        else if ($absDiff / self::MONTH < 12)
        {
            return self::prettyFormat($difference / self::MONTH, 'month');
        }

        // Over a year ago
        return self::prettyFormat($difference / self::YEAR, 'year');
    }
}
