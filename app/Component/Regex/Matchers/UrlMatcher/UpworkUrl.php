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
class UpworkUrl
    extends AbstractRegexMatcher
    implements RegexMatchable
{
    /**
     * Name of the match
     *
     * @var string
     */
    protected $name = 'Upwork';
    
    /**
     * @var string
     */
    protected $expression = 'https?://www\.upwork\.com/(?:freelancers/|o/profiles/users/_)~([^/?]+)';
    
    /**
     * Prematch with strstr to save cpu power
     *
     * @var string
     */
    protected $prematch = 'upwork.com';
}
