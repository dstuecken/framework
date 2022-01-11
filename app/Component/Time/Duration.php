<?php
namespace DS\Component\Time;

use Khill\Duration\Duration as DurationLib;

/**
 * DS-Framework
 *
 * @author Dennis Stücken
 * @license proprietary

 * @copyright Dennis Stücken
 * @link https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component\Time
 */
class Duration
{
    /**
     * @var self
     */
    private static $instance;

    /**
     * @var DurationLib
     */
    private $duration;

    /**
     * Create instance
     *
     * @return Duration
     */
    public static function instance()
    {
        if (!self::$instance)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param string|int $time
     *
     * @return string
     */
    public function humanize($time)
    {
        return $this->duration->humanize($time);
    }

    /**
     * @param string $duration
     *
     * @return int
     */
    public function toSeconds($duration)
    {
        return $this->duration->toSeconds($duration);
    }

    /**
     * @param string $duration
     *
     * @return string
     */
    public function formatted($duration)
    {
        return $this->duration->formatted($duration);
    }

    /**
     * Duration constructor.
     */
    private function __construct()
    {
        $this->duration = new DurationLib();
    }

}
