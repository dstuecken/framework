<?php

namespace DS\Component\Analytics;

use DS\Model\User;

/**
 * DS-Framework
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component
 */
class Mixpanel
{
    /**
     * @var \Mixpanel
     */
    private $mp = null;
    
    /**
     * @var array
     */
    private $timers = [];
    
    /**
     * @param $property
     * @param $value
     */
    public function register($property, $value)
    {
        $this->mp->register($property, $value);
    }
    
    /**
     * @return \Producers_MixpanelPeople
     */
    public function getPeople(): \Producers_MixpanelPeople
    {
        return $this->mp->people;
    }
    
    /**
     * Track an event defined by $event associated with metadata defined by $properties
     *
     * @param string $event
     * @param array  $properties
     *
     * @return $this
     */
    public function track(string $event, array $properties = [])
    {
        if (isset($this->timers[$event]))
        {
            $timeEnd                      = microtime(true);
            $properties['execution_time'] = number_format(($timeEnd - $this->timers[$event]), 1);
        }
        
        $this->mp->track($event, $properties);
        
        return $this;
    }
    
    public function time(string $event)
    {
        $this->timers[$event] = microtime(true);
    }
    
    /**
     * @param int   $userId
     * @param float $amount
     *
     * @return $this
     */
    public function trackCharge(int $userId, float $amount)
    {
        $this->mp->people->trackCharge($userId, $amount, time());
        
        return $this;
    }
    
    /**
     * Identify the user you want to associate to tracked events
     *
     * @param User $user
     *
     * @return $this
     */
    public function identify(User $user)
    {
        $this->mp->identify($user->getId());
        $this->register('userId', $user->getId());
        $this->register('clientId', $user->getClientId());
        
        return $this;
    }
    
    /**
     * Mixpanel constructor.
     *
     * @param string $key
     * @param array  $options
     */
    public function __construct(string $key, $options = [])
    {
        $this->mp = \Mixpanel::getInstance($key, $options);
    }
}
