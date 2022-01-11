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
class LinkedinUrl
    extends AbstractRegexMatcher
    implements RegexMatchable
{
    /**
     * Name of the match
     *
     * @var string
     */
    protected $name = 'Linkedin';
    
    /**
     * @var string
     */
    protected $expression = 'https?://(?:(?:www|mobile|[a-z]{2})\.)?linkedin\.com/(?:(?:in/([^/\"]+))|(?:pub/([a-zA-Z0-9-]+(?:/[a-zA-Z0-9]+){3}))|profile/view\?id=([0-9]+))[/&]?';
    
    /**
     * Prematch with strstr to save cpu power
     *
     * @var string
     */
    protected $prematch = 'linkedin.com';
}
