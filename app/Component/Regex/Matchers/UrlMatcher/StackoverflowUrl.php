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
class StackoverflowUrl
    extends AbstractRegexMatcher
    implements RegexMatchable
{
    /**
     * Name of the match
     *
     * @var string
     */
    protected $name = 'Stack Overflow';
    
    /**
     * @var string
     */
    protected $expression = 'https?:\/\/w?w?w?\.?stackoverflow\.com\/(?!help|company|jobs)(?:users\/([0-9]+)\/[a-z0-9_\-]+|(?!users)([a-z0-9_\-]+)|users\/([a-z0-9_\-]+))';
    
    /**
     * Prematch with strstr to save cpu power
     *
     * @var string
     */
    protected $prematch = 'stackoverflow.com';
}
