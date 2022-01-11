<?php
namespace DS\Component\Mail;

use DS\Component\Queue\QueueInterface;
use Phalcon\Mailer\Message;

/**
 * DS-Framework
 *
 * Mailing
 *
 * @author Dennis Stücken
 * @license proprietary

 * @copyright Dennis Stücken
 * @link https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component\Mail
 */
class MailQueue
{

    /**
     * @var QueueInterface
     */
    private $queue;
    
    /**
     * @param Message $mailMessage
     * @param array   $options
     *
     * @return $this
     */
    public function queue(Message $mailMessage, $options = [])
    {
        $this->queue->queue(
            'mail',
            serialize($mailMessage),
            $options
        );

        return $this;
    }

    /**
     * MailQueue constructor.
     *
     * @param QueueInterface $queue
     */
    public function __construct(QueueInterface $queue)
    {
        $this->queue = $queue;
    }
}
