<?php

namespace DS\Cli\Format;

/**
 * The format class controls everything to do with formating
 *
 * @package    CLI
 * @subpackage Format
 */
class Format
{
    /**
     * Instance of the collection
     *
     * @var Object
     */
    protected static $collection;
    
    /**
     * Set up class
     *
     * @param null $collection
     */
    public function __construct($collection = null)
    {
        static::$collection = (!is_null($collection) ? $collection : new FormatCollection);
    }
    
    /**
     * Adds a format to the collection object
     *
     * @param       $name
     * @param array $details
     */
    public static function addFormat($name, Array $details)
    {
        static::$collection->add($name, $details);
    }
    
    /**
     * @param string $code
     *
     * @return string
     */
    public static function colorFormat(string $code): string
    {
        return "\33[" . $code . "m";
    }
    
    /**
     * @return string
     */
    public static function colorReset(): string
    {
        return "\33[0m";
    }
    
    /**
     * Checks a string for format codes and replaces it with its background/foreground color codes
     *
     * @param $str
     *
     * @return String
     */
    public static function format($str)
    {
        preg_match('/<[A-Za-z0-9]+?>/', $str, $matches);
        
        foreach ($matches as $match)
        {
            $keyword = str_replace(['<', '>'], '', $match);
            $format  = static::$collection->get(strtolower($keyword));
            if (!empty($format))
            {
                $foreground = (!isset($format['foreground']) ? '' : self::colorFormat($format['foreground']));
                $background = (!isset($format['background']) ? '' : self::colorFormat($format['background']));
                $str        = str_replace(
                    ['<' . $keyword . '>', '</' . $keyword . '>'],
                    [$foreground . $background, self::colorReset()],
                    $str
                );
            }
        }
        
        return $str;
    }
    
}