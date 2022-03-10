<?php

namespace DS\Traits;

use Phalcon\Events\Manager;
use Phalcon\Events\ManagerInterface;
use Phalcon\Logger\AdapterInterface;

/**
 * DS-Framework Application
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Controller
 */
trait EventsAwareTrait
{
    /**
     * @var ?ManagerInterface
     */
    protected $eventsManager;
    
    /**
     * Returns events manager instance
     *
     * Note: creates a new instance if no events manager was provided
     *
     * @return ManagerInterface
     */
    public function getEventsManager(): ManagerInterface
    {
        if (!$this->eventsManager)
        {
            $this->setEventsManager(new Manager());
        }
        
        return $this->eventsManager;
    }
    
    /**
     * Set internal events manager instance
     *
     * @param ManagerInterface $eventsManager
     *
     * @return void
     */
    public function setEventsManager(ManagerInterface $eventsManager): void
    {
        $this->eventsManager = $eventsManager;
    }
}
