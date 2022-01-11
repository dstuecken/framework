<?php

namespace DS\Component\Regex\Matchers\ProtocolMatcher;

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
class SkypeProtocollHandler
    extends AbstractRegexMatcher
    implements RegexMatchable
{
    /**
     * Name of the match
     *
     * @var string
     */
    protected $name = 'Skype';
    
    /**
     * @var string
     */
    protected $expression = 'skype:([a-zA-Z0-9_]+)\?call';
    
    /**
     * Prematch with strstr to save cpu power
     *
     * @var string
     */
    protected $prematch = 'skype:';
}
