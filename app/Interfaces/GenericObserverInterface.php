<?php
namespace DS\Interfaces;

/**
 * DS-Framework Application
 *
 * @author Dennis Stücken
 * @license proprietary

 * @copyright Dennis Stücken
 * @link https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Controller
 */
interface GenericObserverInterface
{
    /**
     * Call update function
     *
     * @param mixed $data
     *
     * @return mixed|void
     */
    public function update($data);
}
