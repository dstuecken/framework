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
interface GenericObservable
{
    /**
     * Attach new observer
     *
     * @param GenericObserverInterface $observer
     *
     * @return mixed
     */
    public function attach(GenericObserverInterface $observer);

    /**
     * Notify all observers
     *
     * @param mixed $data
     *
     * @return mixed
     */
    public function notify($data);
}
