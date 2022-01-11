<?php

namespace DS\Component\Mail;

use DS\Component\Links\HomeLink;
use DS\Component\Mail\ViewParams\DefaultParams;
use DS\Component\Queue\QueueInterface;
use DS\Component\View\Volt\VoltAdapter;
use DS\Constants\Services;
use DS\Model\Abstracts\AbstractUser;
use DS\Model\User;
use DS\Traits\DiInjection;
use Phalcon\Di\DiInterface;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mailer\Manager;
use Phalcon\Mailer\Message;

/**
 * DS-Framework
 *
 * Mailing
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component\Mail
 */
class MailEvent
{
    use DiInjection;

    /**
     * @var MailQueue
     */
    protected $queue;

    /**
     * @var int
     */
    protected $lastStatus = 0;

    /**
     * @var string
     */
    protected $lastResponseBody = '';

    /**
     * @var MailManager
     */
    protected $mailManager;

    /**
     * @var Message
     */
    protected $message;

    /**
     * @var string
     */
    protected $viewPath = 'mails/default/layout.volt';

    /**
     * @var string
     */
    protected $subject = '';

    /**
     * @var string
     */
    protected $contentType = Message::CONTENT_TYPE_HTML;

    /**
     * @return int
     */
    public function getLastStatus(): int
    {
        return $this->lastStatus;
    }

    /**
     * @return string
     */
    public function getLastResponseBody(): string
    {
        return $this->lastResponseBody;
    }

    /**
     * @return mixed
     */
    public function getQueue()
    {
        return $this->queue;
    }

    /**
     * @param QueueInterface $queue
     *
     * @return $this
     */
    public function setQueue($queue)
    {
        $this->queue = $queue;

        return $this;
    }

    /**
     * @return MailManager
     */
    public function getMailManager()
    {
        return $this->mailManager;
    }

    /**
     * @param Manager $mailer
     *
     * @return $this
     */
    public function setMailManager($mailManager)
    {
        $this->mailManager = $mailManager;

        return $this;
    }

    /**
     * @param bool $force
     *
     * @return $this
     * @throws Exceptions\MailSendException
     * @throws \SendGrid\Mail\TypeException
     */
    public function send($force = false)
    {
        // Send all emails to a specific email address in development (for testing purposes)
        if ($this->serviceManager->getConfig()->get('mode') === 'development')
        {
            $this->message->to($this->serviceManager->getConfig()->get('mail')['test-email']);
            $force = true;
        }
        // Send all emails to a specific email address in test (for testing purposes)
        elseif ($this->serviceManager->getConfig()->get('mode') === 'test')
        {
            $this->message->to($this->serviceManager->getConfig()->get('mail')['test-email']);
            $force = true;
        }

        // Only send mails in production
        if ($this->serviceManager->getConfig()->get('mode') === 'production' || $force)
        {
            //@todo enable queuing maybe?
            //$this->queue->queue($this->message);

            $this->lastStatus       = $this->mailManager->send($this->message);
            $this->lastResponseBody = $this->mailManager->getLastResponseBody();
        }

        $this->serviceManager->getMixpanel()->track('Email.Send', ['type' => get_class($this), 'subject' => $this->subject, 'to' => implode(', ', $this->message->getTo())]);

        return $this;
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function prepareUrl(string $path = '/'): string
    {
        return HomeLink::get($path);
    }

    /**
     * @param DefaultParams $viewParams
     *
     * @return string
     */
    protected function buttonNotWorkingMessage(ViewParams\DefaultParams $viewParams)
    {
        return $viewParams->buttonLink ? sprintf('Button not working? Paste the following link into your browser: %s', $viewParams->buttonLink) : '';
    }

    /**
     * Prepare a message that is going to be send to a user
     *
     * @param DefaultParams     $viewParams
     * @param AbstractUser|null $userModel
     *
     * @return Message
     */
    protected function prepareUserMessage(DefaultParams $viewParams, AbstractUser $userModel = null): Message
    {
        /**
         * Creating a new Volt view instance here since using
         * $this->mailmanager->createMessageFromView did not work!
         *
         * Original code was just: $this->message =
         * $this->mailManager->createMessageFromView($this->viewPath, $viewParams->toArray())
         * ->to($userModel->getEmail(), $userModel->getName())
         * ->subject($this->subject);
         */
        $this->message = $this->mailManager->createMessage();

        $view = new \Phalcon\Mvc\View();
        $view->setViewsDir(ROOT_PATH . '/app/Views/');
        $volt = new VoltAdapter($view, $this->getDI(), application());

        if ($this->viewPath)
        {
            // Start output buffering since $volt->render is not returning the html somehow. Seems like a bug.
            // So trying to output and get it from there for now..
            ob_start();
            $volt->render(ROOT_PATH . '/app/Views/' . $this->viewPath, $viewParams->toArray());
            $mailContent = ob_get_contents();
            ob_end_clean();

            $this->message->content($mailContent, $this->contentType);
        }
        else
        {
            $this->message->content($viewParams->topMessage, $this->contentType);
        }

        if ($userModel)
        {
            $this->message->to($userModel->getEmail(), $userModel->getName());
        }

        $this->message->subject($this->subject);

        return $this->message;
    }

    /**
     * @param User $user
     *
     * @return MailEvent
     */
    public function setToUser(User $user): MailEvent
    {
        $this->message->to($user->getEmail(), $user->getName());

        return $this;
    }

    /**
     * @param FactoryDefault|DiInterface $di
     *
     * @return $this
     */
    public static function factory(DiInterface $di)
    {
        $self = new static($di);

        //$self->setQueue($di->get(Services::QUEUE));

        $self->setMailManager($di->get(Services::MAILER));

        return $self;
    }

}
