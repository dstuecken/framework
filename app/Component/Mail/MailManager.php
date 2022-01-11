<?php

namespace DS\Component\Mail;

use DS\Component\Mail\Exceptions\MailSendException;
use Phalcon\Logger;
use Phalcon\Mailer\Manager;
use Phalcon\Mailer\Message;
use SendGrid\Mail\TypeException;

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
class MailManager extends Manager
{
    private $lastStatus;

    private $lastResponseBody = '';

    /**
     * @return mixed
     */
    public function getLastStatus()
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
     * Returns status code. Throws if there is an error.
     *
     * @param Message $message
     * @param null    $templateId
     *
     * @return int
     * @throws MailSendException
     * @throws \SendGrid\Mail\TypeException
     */
    public function send(Message $message, $templateId = null)
    {
        try
        {
            $email = new \SendGrid\Mail\Mail();

            if (is_array($message->getTo()))
            {
                $email->addTos($message->getTo());
            }
            else
            {
                $to = (string) $message->getTo();
                $email->addTos([$to => $to]);
            }

            $email->setSubject($message->getSubject());
            $email->addContent(
                "text/html",
                $message->getContent()
            );

            foreach ($message->getFrom() as $address => $name)
            {
                $email->setFrom($address, $name);
                break;
            }

            if ($templateId !== null)
            {
                $email->setTemplateId($templateId);
            }
        }
        catch (TypeException $e)
        {
            application()->log($e->getMessage(), Logger::ERROR);

            throw new MailSendException("Error sending mail. (".$e->getMessage().")");
        }

        /**
         * @var $sendgrid \SendGrid
         */
        try
        {
            $sendgrid = $this->di->getShared('sendgrid');
            $response = $sendgrid->send($email);
            
            if ($response->statusCode() < 400)
            {
                $this->lastStatus       = $response->statusCode();
                $this->lastResponseBody = $response->body();

                return $response->statusCode();
            }

            throw new MailSendException("Error sending mail. (" . $response->statusCode() . ")", $response->body(), $response->statusCode());
        }
        catch (MailSendException $e) {
            if ($e->getBody()) {
                $body = json_decode($e->getBody());
                if (isset($body->errors[0]->message)) {
                    throw new MailSendException("Error sending mail: " . $body->errors[0]->message);
                }
            }
            
            throw new MailSendException("Error sending mail: " . $e->getMessage() . '.');
        }
        catch (\Exception $e)
        {
            throw new MailSendException("Error sending mail: " . $e->getMessage() . '.');
        }
    }
}
