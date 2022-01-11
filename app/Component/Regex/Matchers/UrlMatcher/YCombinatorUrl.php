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
class YCombinatorUrl
    extends AbstractRegexMatcher
    implements RegexMatchable
{
    /**
     * Name of the match
     *
     * @var string
     */
    protected $name = 'YCombinator';
    
    /**
     * preg_match expression
     *
     * @var string
     */
    protected $expression = 'https?://news\.ycombinator\.com/user\?id=([^/"]+)';
    
    /**
     * Prematch with strstr to save cpu power
     *
     * @var string
     */
    protected $prematch = 'ycombinator.com';
}
