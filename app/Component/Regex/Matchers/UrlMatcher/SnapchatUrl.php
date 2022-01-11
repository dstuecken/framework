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
class SnapchatUrl
    extends AbstractRegexMatcher
    implements RegexMatchable
{
    /**
     * Name of the match
     *
     * @var string
     */
    protected $name = 'Snapchat';
    
    /**
     * preg_match expression
     *
     * @var string
     */
    protected $expression = '((https?:\/\/)?(www\.)?snapchat\.com\/)?(\/add\/)?(([a-z0-9._-]{2,16}))?(\/)?';
    
    /**
     * Prematch with strstr
     *
     * @var string
     */
    protected $prematch = 'snapchat.com';
}
