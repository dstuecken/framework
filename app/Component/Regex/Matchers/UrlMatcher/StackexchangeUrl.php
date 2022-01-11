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
class StackexchangeUrl
    extends AbstractRegexMatcher
    implements RegexMatchable
{
    /**
     * Name of the match
     *
     * @var string
     */
    protected $name = 'Stack Exchange';
    
    /**
     * @var string
     */
    protected $expression = 'http(?:s)?:\/\/(?:www\.)?stackexchange\.com\/(?!sites|leagues|legal)(?:users\/[0-9]+|(?!users)[a-z0-9_\-]+)';
    
    /**
     * Prematch with strstr to save cpu power
     *
     * @var string
     */
    protected $prematch = 'stackexchange.com';
}
