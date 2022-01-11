<?php

namespace DS\Component\Text;

use DS\Component\PrettyDateTime;

/**
 * DS-Framework
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component
 */
class StringFormat
{
    /**
     * @var PrettyDateTime
     */
    private $date = null;
    
    /**
     * @return $this
     */
    public function init()
    {
        if (!$this->date)
        {
            $this->date = new PrettyDateTime();
        }
        
        return $this;
    }
    
    /**
     * @return StringFormat
     */
    public static function factory()
    {
        return (new self())->init();
    }
    
    /**
     * @param string $string
     * @param int    $len
     * @param string $suffix
     *
     * @return string
     */
    public static function strip(string $string, $len = 120, $suffix = '..'): string
    {
        if (strlen($string) > $len)
        {
            return substr($string, 0, $len) . $suffix;
        }
        
        return $string;
    }
    
    /**
     * @param string $text
     *
     * @return string
     */
    public function markdown(string $text)
    {
        $parseDown = \Parsedown::instance();
        
        return $parseDown->parse($text);
    }
    
    /**
     * @param $date
     *
     * @return string
     * @throws \Exception
     */
    public function prettyDay(string $date)
    {
        return $this->date->day(new \DateTime($date));
    }
    
    /**
     * @param string $date
     *
     * @return string
     * @throws \Exception
     */
    public function prettyDate(string $date)
    {
        return $this->date->parse(new \DateTime($date));
    }
    
    /**
     * @param string $date
     *
     * @return string
     * @throws \Exception
     */
    public function prettyDateMonth(int $timestamp)
    {
        return date('F Y', $timestamp);
    }
    
    /**
     * @param int $timestamp
     *
     * @return string
     * @throws \Exception
     */
    public function prettyDateTimestamp(int $timestamp): string
    {
        return $this->date->parse(new \DateTime(date('Y-m-d H:i:s', $timestamp)));
    }
    
    /**
     * @param string $price
     *
     * @return string
     */
    public function prettyPrice(string $price, int $decimals = 0): string
    {
        return number_format((float) $price, $decimals);
    }
    
    /**
     * @param mixed $number
     *
     * @return string
     */
    public function prettyNumber($number): string
    {
        if (is_null($number))
        {
            return 0;
        }
        
        $readable = ["", "k", "M", "B", "T"];
        $index    = 0;
        while ($number > 1000)
        {
            $number /= 1000;
            $index++;
        }
        
        return ("" . number_format(round($number, 10), $number > 1000 ? 1 : 0) . (isset($readable[$index]) ? $readable[$index] : '..'));
    }
    
}
