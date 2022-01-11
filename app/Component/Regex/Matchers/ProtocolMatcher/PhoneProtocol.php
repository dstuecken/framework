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
class PhoneProtocol
    extends AbstractRegexMatcher
    implements RegexMatchable
{
    /**
     * Name of the match
     *
     * @var string
     */
    protected $name = 'Phone';
    
    /**
     * @var string
     */
    protected $expression = '(?:tel|phone|mobile):(\+?[0-9. -]+)';
}
