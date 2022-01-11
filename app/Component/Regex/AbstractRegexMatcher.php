<?php

namespace DS\Component\Regex;

use DS\Component\Regex\Exceptions\MatchExpressionNotSetException;
use DS\Traits\Factory;

/**
 * DS
 *
 * @copyright 2017 | Dennis Stücken
 *
 * @version   $Version$
 * @package   DS\Component
 */
abstract class AbstractRegexMatcher implements RegexMatchable
{
    use Factory;
    
    /**
     * The regex expression to match
     *
     * @var string
     */
    protected $expression = null;
    
    /**
     * preg_match Flags
     *
     * @var string
     */
    protected $flags = 'is';
    
    /**
     * Name ofn the
     *
     * @var string
     */
    protected $name = '';
    
    /**
     * Prematch with strstr to save cpu power
     *
     * @var string
     */
    protected $prematch = '';
    
    /**
     * @return string
     */
    protected function provideRegexPattern(): string
    {
        //                      match string ending for /, " or EOL ($)
        //                                         \/
        return '#' . $this->getExpression() . '(?:/|"|$)#' . $this->getFlags();
    }
    
    /**
     * Match override to be used in your Matcher
     *
     * @param string $string
     *
     * @return MatchResult
     * @throws MatchExpressionNotSetException
     */
    public function match(string $string): MatchResult
    {
        if ($this->expression === null)
        {
            throw new MatchExpressionNotSetException('Could not match string: Expression was not set.');
        }
        
        // $prematch = false;
        
        // Debug:
        //echo "\n String: " . $string;
        //echo "\n" . "Matching " . '#' . $this->getExpression() . '#' . $this->getFlags() . "";
        
        // Prematch with strstr if prematch string is set since this is way less cpu intensive for strong recursions
        if ($this->prematch !== '')
        {
            $prematch = strpos($string, $this->prematch) !== false;
            if (!$prematch)
            {
                return new MatchResult(
                    false, [], $this->getName()
                );
            }
        }
        
        // Register reference variable
        $register = [];
        
        // Match delimiter # so that forward slash (/) doesn't have to be escaped, Tilde (~) was also not possible
        // http://stackoverflow.com/questions/3145264/regular-expression-and-forward-slash
        $matched = preg_match($this->provideRegexPattern(), $string, $register);
        
        // False positive detection (todo: log this somewhere)
        //if ($matched === false && $prematch === true) {
        //    echo "\nPossible false positive since prematch was successfull for " . $this->getName() . "\n";
        //}
        
        return new MatchResult(
            $matched, $register, $this->getName()
        );
    }
    
    /**
     * @return string
     */
    public function getExpression(): string
    {
        return $this->expression;
    }
    
    /**
     * @return string
     */
    public function getFlags(): string
    {
        return $this->flags;
    }
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
}
