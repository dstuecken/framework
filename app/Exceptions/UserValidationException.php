<?php

namespace DS\Exceptions;

use Phalcon\Exception;

/**
 * https://www.dvlpr.de
 *
 * @author Dennis Stücken
 * @license proprietary

 * @copyright https://www.dvlpr.de
 * @link https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Controller
 */
class UserValidationException extends Exception
{
    /**
     * @var string
     */
    private $field = '';

    /**
     * @var string
     */
    private $value = '';

    /**
     * @var string
     */
    private $fixMessage = '';

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @param string $field
     *
     * @return $this
     */
    public function setField($field)
    {
        $this->field = $field;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     *
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getFixMessage()
    {
        return $this->fixMessage;
    }

    /**
     * @param string $fixMessage
     *
     * @return $this
     */
    public function setFixMessage($fixMessage)
    {
        $this->fixMessage = $fixMessage;

        return $this;
    }

    /**
     * UserValidationException constructor.
     *
     * @param string $message
     * @param string $field
     * @param string $value
     * @param string $fixMessage
     */
    public function __construct($message, $field = '', $value = '', $fixMessage = '')
    {
        parent::__construct($message);

        $this->field      = $field;
        $this->value      = $value;
        $this->fixMessage = $fixMessage;
    }
}
