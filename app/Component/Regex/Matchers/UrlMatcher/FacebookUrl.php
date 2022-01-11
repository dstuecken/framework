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
class FacebookUrl
    extends AbstractRegexMatcher
    implements RegexMatchable
{
    /**
     * Name of the match
     *
     * @var string
     */
    protected $name = 'Facebook';
    
    /**
     * Does not match Facbook Pixels - identified by "tr?id="
     *
     * @var string
     */
    protected $expression = 'https?:\/\/(?:www\.)?(?:facebook|fb)\.com\/(?!tr\?id=)([^/\"]+)';
    
    /**
     * Prematch with strstr to save cpu power
     *
     * @var string
     */
    protected $prematch = '.com';
    
    /**
     * Database field in profileHandles
     *
     * @var string
     */
    protected $dbField = 'facebook';
}
