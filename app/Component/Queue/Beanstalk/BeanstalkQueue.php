<?php
namespace DS\Component\Queue\Beanstalk;

use DS\Component\Queue\QueueInterface;
use Phalcon\Queue\Beanstalk\Extended;

/**
 * DS-Framework
 *
 * Queueing
 *
 * @deprecated Beanstalk is deprecated. Use Bernard instead.
 *
 * @author Dennis Stücken
 * @license proprietary

 * @copyright Dennis Stücken
 * @link https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component\Mail
 */
class BeanstalkQueue implements QueueInterface
{
    /**
     * @var Extended
     */
    private $queue;

    /**
     * @param string $queueName
     * @param string $data
     * @param array  $options
     */
    public function queue(string $queueName, array $data, $options = []): QueueInterface
    {
        $this->queue->putInTube($queueName, $data, $options);

        return $this;
    }

    public function __construct(Extended $beanstalk)
    {
        $this->queue = $beanstalk;
    }
}
