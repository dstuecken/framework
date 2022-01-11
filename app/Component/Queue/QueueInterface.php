<?php
namespace DS\Component\Queue;

/**
 * DS-Framework
 *
 * Queueing
 *
 * @author Dennis Stücken
 * @license proprietary

 * @copyright Dennis Stücken
 * @link https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component\Mail
 */
interface QueueInterface
{
    /**
     * @param string $queueName
     * @param string $data
     * @param array  $options
     */
    public function queue(string $queueName, array $data, $options = []): QueueInterface;
}
