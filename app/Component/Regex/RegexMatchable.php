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
interface RegexMatchable
{
    /**
     * @param string $string
     *
     * @return MatchResult
     */
    public function match(string $string);
}
