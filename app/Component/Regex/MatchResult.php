<?php

namespace DS\Component\Regex;

/**
 * DS
 *
 * @copyright 2017 | Dennis Stücken
 *
 * @version   $Version$
 * @package   DS\Component
 */
class MatchResult
{
    /**
     * Regex register of matches, first index should always be the matched result
     *
     * @var array
     */
    private $register = [];
    
    /**
     * Is it a match or not?
     *
     * @var bool
     */
    private $matched = false;
    
    /**
     * Textual identifier for this match (Like Twitter or E-Mail)
     *
     * @var string
     */
    private $identifier = '';
    
    /**
     * @return array
     */
    public function getRegister(): array
    {
        return $this->register;
    }
    
    /**
     * @param int $index
     *
     * @return string
     */
    public function getRegisterIndex(int $index): string
    {
        return isset($this->register[$index]) ? $this->register[$index] : '';
    }
    
    /**
     * @return bool
     */
    public function isMatched(): bool
    {
        return $this->matched;
    }
    
    /**
     * @return string
     */
    public function getIdentifier(): string
    {
        return $this->identifier;
    }
    
    /**
     * MatchResult constructor.
     *
     * @param bool   $matched
     * @param array  $register
     * @param string $identifier
     */
    public function __construct(bool $matched, array $register, string $identifier = '')
    {
        $this->matched       = $matched;
        $this->register      = $register;
        $this->identifier    = $identifier;
    }
}
