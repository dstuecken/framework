<?php

namespace DS\Component\Regex\Matchers\UrlMatcher;

use DS\Component\Regex\AbstractRegexMatcher;
use DS\Component\Regex\RegexMatchable;

/**
 * DS
 *
 * @copyright 2017 | Dennis Stücken
 *
 * @version   $Version$
 * @package   DS\Component
 */
class HackernewsUrl
    extends AbstractRegexMatcher
    implements RegexMatchable
{
    /**
     * Name of the match
     *
     * @var string
     */
    protected $name = 'Hackernews';
    
    /**
     * @var string
     */
    protected $expression = 'https?://hackernewsers.com/users/(.*?)\.html';
    
    /**
     * Prematch with strstr to save cpu power
     *
     * @var string
     */
    protected $prematch = 'hackernewsers.com';
}
