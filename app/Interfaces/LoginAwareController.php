<?php
namespace DS\Interfaces;

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
interface LoginAwareController
{
    /**
     * Handle ajax request
     *
     * @return mixed
     */
    public function needsLogin();
}
