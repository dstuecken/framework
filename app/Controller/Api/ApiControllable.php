<?php
namespace DS\Controller\Api;

/**
 *
 * DS-Framework
 *
 * @author Dennis Stücken
 * @license proprietary

 * @copyright Dennis Stücken
 * @link https://www.dvlpr.de
 *
 * @version $Version$
 * @package DS\Controller
 */
interface ApiControllable
{

    /**
     * Process api request
     *
     * Return value is directly sent to the requestor
     *
     * @return mixed
     */
    public function process();

}
