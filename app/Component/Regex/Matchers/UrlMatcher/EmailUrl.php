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
class EmailUrl
    extends AbstractRegexMatcher
    implements RegexMatchable
{
    /**
     * Name of the match
     *
     * @var string
     */
    protected $name = 'E-Mail';
    
    /**
     * @var string
     */
    protected $expression = '[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})';
    
    /**
     * Prematch with strstr to save cpu power
     *
     * @var string
     */
    protected $prematch = '@';
    
    /**
     * Overriding regex pattern since we don't need the greedy (?:/|") operation at the end here
     *
     * @return string
     */
    protected function provideRegexPattern(): string
    {
        return '#' . $this->getExpression() . '#' . $this->getFlags();
    }
}
