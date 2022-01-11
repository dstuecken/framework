<?php

namespace DS\Component\Queue\Bernard;

use Bernard\Consumer;
use Bernard\Message\PlainMessage;
use Bernard\Producer;
use Bernard\QueueFactory;
use Bernard\Receiver;
use Bernard\Router\ReceiverMapRouter;
use DS\Component\Queue\QueueInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

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
class BernardQueue implements QueueInterface
{
    /**
     * @var QueueFactory
     */
    private $queue;

    /**
     * @var Producer
     */
    private $producer;

    /**
     * @var array
     */
    private $options = [];

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param array $options
     *
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $options;

        return $this;
    }

    /**
     * @param string $queueName
     * @param array  $data
     * @param array  $options
     */
    public function queue(string $queueName, array $data, $options = []): QueueInterface
    {
        $message = new PlainMessage($queueName, $data);
        $this->producer->produce($message);

        return $this;
    }

    /**
     * @param string   $name
     * @param Receiver $listener
     *
     * @return BernardQueue
     */
    public function consume(string $name, Receiver $listener): BernardQueue
    {
        $router = new ReceiverMapRouter(
            [
                $name => $listener,
            ]
        );

        $consumer = new Consumer($router, new EventDispatcher());
        $consumer->consume($this->queue->create($name), $this->options);

        return $this;
    }

    /**
     * BernardQueue constructor.
     *
     * @param QueueFactory $queue
     * @param array        $options
     */
    public function __construct(QueueFactory $queue, array $options = [])
    {
        $this->queue    = $queue;
        $this->options  = $options;
        $this->producer = new Producer($this->queue, new EventDispatcher());
    }
}
