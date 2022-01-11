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
class VKUrl
    extends AbstractRegexMatcher
    implements RegexMatchable
{
    /**
     * Name of the match
     *
     * @var string
     */
    protected $name = 'VK';
    
    /**
     * @var string
     */
    protected $expression = 'https?://w?w?w?\.?vk\.com\/([^/"\#\$\;]+)';
    
    /**
     * Prematch with strstr to save cpu power
     *
     * @var string
     */
    protected $prematch = 'vk.com';
}
