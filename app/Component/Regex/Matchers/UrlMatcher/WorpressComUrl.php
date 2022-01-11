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
class WorpressComUrl
    extends AbstractRegexMatcher
    implements RegexMatchable
{
    /**
     * Name of the match
     *
     * @var string
     */
    protected $name = 'Wordpress';
    
    /**
     * @var string
     */
    protected $expression = 'http(?:s)?:\/\/([a-zA-Z0-9-]+)?.wordpress\.com\/(?!feed)(?:[^/"\#]+)?';
    
    /**
     * Prematch with strstr to save cpu power
     *
     * @var string
     */
    protected $prematch = 'wordpress.com';
}
