<?php
namespace DS\Traits\Controller;

/**
 * DS-Framework
 *
 * @author Dennis Stücken
 * @license proprietary

 * @copyright Dennis Stücken
 * @link https://www.dvlpr.de
 *
 * @version $Version$
 * @package DS\Interfaces
 */
trait DoesNotNeedLoginTrait
{
    /**
     * @return bool
     */
    public function needsLogin()
    {
        return false;
    }
}
