<?php

namespace DS\Component;

use DS\Traits\DiInjection;

/**
 * DS-Framework
 *
 * @author Dennis Stücken
 * @license proprietary

 * @copyright Dennis Stücken
 * @link https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component
 */
class Notify
{
    use DiInjection;
    
    private static $counter = 0;
    
    /**
     * Send a notify message via HTTP Header X-Notify-$counter
     *
     * @param string $message
     */
    public function headerMessage(string $message)
    {
        $this->serviceManager->getResponse()->setHeader('X-Notify-' . self::$counter++, $message);
    }
    
}
