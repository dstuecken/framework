<?php

namespace DS\Traits;

use DS\Interfaces\GenericObserverInterface;

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
trait GenericObservableTrait
{
    /**
     * @var GenericObserverInterface[]
     */
    private $observers = [];
    
    /**
     * Attach new observer
     *
     * @param GenericObserverInterface $observer
     *
     * @return $this
     */
    public function attach(GenericObserverInterface $observer)
    {
        $this->observers[] = $observer;
        
        return $this;
    }
    
    /**
     * Notify all observers
     *
     * @param mixed $data
     */
    public function notify($data)
    {
        foreach ($this->observers as $observer)
        {
            $observer->update($data);
        }
    }
}
