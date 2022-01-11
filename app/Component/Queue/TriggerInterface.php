<?php

namespace DS\Component\Queue;

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
interface TriggerInterface
{
    /**
     * Define trigger string for this queuing event
     *
     * @return string
     */
    public static function getTrigger(): string;
}
