<?php

namespace DS\Component\Queue;

use Bernard\Message;

/**
 * DS-Framework
 *
 * Queueing
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component\Mail
 */
interface ListenerInterface
{
    /**
     * @param Message $message
     *
     * @return mixed
     */
    public function receive(Message $message);
    
    /**
     * Initialize the listener
     *
     * @return mixed
     */
    public function initialize(): void;
}
