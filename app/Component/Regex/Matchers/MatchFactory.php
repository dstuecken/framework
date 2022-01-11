<?php

namespace DS\Component\Regex\Matchers;

use DS\Component\Regex\RegexMatchable;
use Symfony\Component\Finder\Finder;

/**
 * DS
 *
 * @copyright 2017 | Dennis Stücken
 *
 * @version   $Version$
 * @package   DS\Component
 */
class MatchFactory
{
    /**
     * In-memory cache for found url matchers
     *
     * @var array
     */
    private static $urlmatchersCache = [];
    
    /**
     * @return RegexMatchable[]
     */
    private static function getClassesForPath($path)
    {
        // Create in-memory cache for url matchers
        if (!self::$urlmatchersCache[$path])
        {
            $finder = new Finder();
            $files  = $finder->files()->in(__DIR__ . '/' . $path);
    
            self::$urlmatchersCache[$path] = [];
            foreach ($files as $file)
            {
                // chr(92) = backslash
                $className = '\DS\Component\Regex\Matchers' . chr(92) . $path . chr(92) . substr($file->getFilename(), 0, -4);
        
                if (class_exists($className))
                {
                    self::$urlmatchersCache[$path][] = new $className;
                }
            }
        }
        
        return self::$urlmatchersCache[$path];
    }
    
    /**
     * @return RegexMatchable[]
     */
    public static function getUrlMatchers()
    {
        return self::getClassesForPath('UrlMatcher');
    }
    
    /**
     * @return RegexMatchable[]
     */
    public static function getProtocolMatchers()
    {
        return self::getClassesForPath('ProtocolMatcher');
    }
}
