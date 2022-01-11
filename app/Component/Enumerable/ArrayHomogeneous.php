<?php

namespace DS\Component\Enumerable;

/**
 * DS-Framework
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component\Text
 */
class ArrayHomogeneous
{
    /**
     * Checks if an array contains at most 1 distinct value.
     * Optionally, restrict what the 1 distinct value is permitted to be via
     * a user supplied testValue.
     *
     * @param array $arr       - Array to check
     * @param null  $testValue - Optional value to restrict which distinct value the array is permitted to contain.
     *
     * @return bool - false if the array contains more than 1 distinct value, or contains a value other than your supplied testValue.
     * @assert isHomogenous([]) === true
     * @assert isHomogenous([], 2) === true
     * @assert isHomogenous([2]) === true
     * @assert isHomogenous([2, 3]) === false
     * @assert isHomogenous([2, 2]) === true
     * @assert isHomogenous([2, 2], 2) === true
     * @assert isHomogenous([2, 2], 3) === false
     * @assert isHomogenous([2, 3], 3) === false
     * @assert isHomogenous([null, null], null) === true
     */
    public static function isHomogenous(array $arr, $testValue = null)
    {
        // If they did not pass the 2nd func argument, then we will use an arbitrary value in the $arr (that happens to be the first value).
        // By using func_num_args() to test for this, we can properly support testing for an array filled with nulls, if desired.
        // ie isHomogenous([null, null], null) === true
        $testValue = func_num_args() > 1 ? $testValue : reset($arr);
        foreach ($arr as $val)
        {
            if ($testValue !== $val)
            {
                return false;
            }
        }
        
        return true;
    }
}
