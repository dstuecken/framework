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
class LinkedinPublicUrl
    extends AbstractRegexMatcher
    implements RegexMatchable
{
    /**
     * Name of the match
     *
     * @var string
     */
    protected $name = 'Linkedin Public';
    
    /**
     * @var string
     */
    // Matched currently disabled
    protected $expression = '!!!!! https?:\/\/([\w]+\.)?linkedin\.com\/pub\/[A-z 0-9 _ -]+(\/[A-z 0-9]+){3}\/?';
    
    /**
     * Prematch with strstr to save cpu power
     *
     * @var string
     */
    protected $prematch = 'linkedin.com';
}
