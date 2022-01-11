<?php

namespace DS\Component\Mail\Exceptions;

use Phalcon\Exception;

/**
 * DS-Framework
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Controller
 */
class MailSendException extends Exception
{
    /**
     * @var string
     */
    private $body;

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    public function __construct($message = "", $body = '', $code = 0)
    {
        parent::__construct($message, $code);
        $this->body = $body;
    }
}
