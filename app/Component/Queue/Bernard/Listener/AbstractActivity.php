<?php

namespace DS\Component\Queue\Bernard\Listener;

use Bernard\Message\PlainMessage;
use Bernard\Receiver;
use DS\Component\Queue\Bernard\Listener\User\Exception\MessageFlowException;
use DS\Component\Queue\TriggerInterface;

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
abstract class AbstractActivity implements Receiver, TriggerInterface
{
    /**
     * @var array
     */
    protected $requiredMessageParams = [];

    /**
     * Queue
     *
     * @param array $data
     */
    abstract public static function queue(array $data);

    /**
     * @param PlainMessage $message
     *
     * @throws MessageFlowException
     */
    protected function validatedMessageParams(PlainMessage $message)
    {
        foreach ($this->requiredMessageParams as $param)
        {
            if (!$message->has($param))
            {
                throw new MessageFlowException(sprintf('There was a parameter error. "%s" does not exist in the message.', $param), $message);
            }
        }
    }
}
