<?php

namespace DS\Model\Helper;

use DS\Traits\Factory;

/**
 * DS-Framework
 *
 * DateRange model
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Model
 */
class DateRange
{
    use Factory;

    /**
     * @var int
     */
    private $from;

    /**
     * @var int
     */
    private $to;

    /**
     * @return int
     */
    public function getFrom(): int
    {
        return $this->from;
    }

    /**
     * @param int $from
     *
     * @return $this
     */
    public function setFrom($from)
    {
        $this->from = $from;

        return $this;
    }

    /**
     * @return int
     */
    public function getTo(): int
    {
        return $this->to;
    }

    /**
     * @param int $to
     *
     * @return $this
     */
    public function setTo($to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * Today range
     *
     * @return DateRange
     */
    public static function initToday(): DateRange
    {
        $timestamp = strtotime('today');

        return self::factory()
                   ->setFrom($timestamp)
                   ->setTo($timestamp + Seconds::DaysOne - 1);
    }

    /**
     * Yesterday range
     *
     * @return DateRange
     */
    public static function initYesterday(string $ofTimestamp = ''): DateRange
    {
        if ($ofTimestamp)
        {
            $timestamp = strtotime(date('Y-m-d', $ofTimestamp));;
            $yesterdayEnd = strtotime(date('Y-m-d 23:59:59', $ofTimestamp));;
        }
        else
        {
            $timestamp = strtotime('yesterday');
            $yesterdayEnd = strtotime('yesterday 23:59:59');
        }

        return self::factory()
                   ->setFrom($timestamp)
                   ->setTo($yesterdayEnd);
    }

    /**
     * this week (monday-sunday) range
     *
     * @return DateRange
     */
    public static function initThisWeek(): DateRange
    {
        $timestamp = strtotime('this week');

        return self::factory()
                   ->setFrom($timestamp)
                   ->setTo(time());
    }

    /**
     * last week (monday-sunday) range
     *
     * @return DateRange
     */
    public static function initLastWeek(): DateRange
    {
        $timestamp = strtotime('last week');

        return self::factory()
                   ->setFrom($timestamp)
                   ->setTo($timestamp + Seconds::WeeksOne - 1);
    }

    /**
     * This month (01.-30/31.) range
     *
     * @return DateRange
     */
    public static function initThisMonth(): DateRange
    {
        $timestamp = strtotime('this month');

        return self::factory()
                   ->setFrom($timestamp)
                   ->setTo(strtotime('last day of this month 23:59:59'));
    }

    /**
     * Last month (01.-30/31.) range
     *
     * @return DateRange
     */
    public static function initLastMonth(): DateRange
    {
        $timestamp = strtotime('last month');

        return self::factory()
                   ->setFrom($timestamp)
                   ->setTo(strtotime('last day of last month 23:59:59'));
    }

    /**
     * Days to today range
     *
     * @return DateRange
     */
    public static function initLastDays(int $days): DateRange
    {
        $timestamp = strtotime('-' . $days . ' days');

        return self::factory()
                   ->setFrom($timestamp)
                   ->setTo(strtotime('- ' . $days . ' 23:59:59'));
    }

    /**
     * Days to today range
     *
     * @return DateRange
     */
    public static function initDayFromTodayBackwards(int $day): DateRange
    {
        $timestamp = strtotime('-' . $day . ' days');

        return self::factory()
                   ->setFrom($timestamp)
                   ->setTo($timestamp + Seconds::DaysOne - 1);
    }

    /**
     * Init last year 01.01-31.12
     *
     * @return DateRange
     */
    public static function initLastYear(): DateRange
    {
        return self::factory()
                   ->setFrom(strtotime('01/01 last year'))
                   ->setTo(strtotime('12/31 23:59:59 last year'));
    }

    /**
     * Init last year 01.01-31.12
     *
     * @return DateRange
     */
    public static function initThisYear(): DateRange
    {
        return self::factory()
                   ->setFrom(strtotime('01/01 this year'))
                   ->setTo(strtotime('12/31 23:59:59 this year'));
    }

    /**
     * DateRange constructor.
     *
     * @param int $from
     * @param int $to
     */
    public function __construct(int $from = 0, int $to = 0)
    {
        $this->from = $from;
        $this->to   = $to;
    }
}
