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
class Url
{
    /**
     * @param array $parsedUrl
     *
     * @return string
     */
    public static function unparse(array $parsedUrl): string
    {
        $scheme   = isset($parsedUrl['scheme']) ? $parsedUrl['scheme'] . '://' : 'http://';
        $host     = isset($parsedUrl['host']) ? $parsedUrl['host'] : '';
        $port     = isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '';
        $user     = isset($parsedUrl['user']) ? $parsedUrl['user'] : '';
        $pass     = isset($parsedUrl['pass']) ? ':' . $parsedUrl['pass'] : '';
        $pass     = ($user || $pass) ? "$pass@" : '';
        $path     = isset($parsedUrl['path']) ? $parsedUrl['path'] : '';
        $query    = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';
        $fragment = isset($parsedUrl['fragment']) ? '#' . $parsedUrl['fragment'] : '';
        
        return "$scheme$user$pass$host$port$path$query$fragment";
    }
    
    /**
     * @param string $url
     *
     * @return array
     */
    public static function parse(string $url): array
    {
        return parse_url($url);
    }
    
    /**
     * @param string $url
     *
     * @return string
     */
    public static function getRootDomain(string $url): string
    {
        $urlParts = self::parse($url);
        unset($urlParts['path'], $urlParts['query'], $urlParts['fragment']);
        
        return self::unparse($urlParts);
    }
}
