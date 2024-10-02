<?php
namespace DS\Controller\Api\Meta;

/**
 *
 * DS-Framework
 *
 * @author Dennis Stücken
 * @license proprietary

 * @copyright Dennis Stücken
 * @link https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Controller
 */
final class Error implements \JsonSerializable
{
    /**
     * @var string
     */
    private $devMessage;

    /**
     * @var string
     */
    private $error;

    /**
     * @var int
     */
    private $errorCode;

    /**
     * @var string
     */
    private $userMessage;

    /**
     * @var string
     */
    private $more;

    /**
     * Json serializer
     */
    public function jsonSerialize()
    {
        return [
            'error' => $this->error,
            'devMessage' => $this->devMessage,
            'errorCode' => $this->errorCode,
            'userMessage' => $this->userMessage,
            'more' => $this->more,
        ];
    }

    /**
     * @return string
     */
    public function getDevMessage()
    {
        return $this->devMessage;
    }

    /**
     * @param string $devMessage
     *
     * @return $this
     */
    public function setDevMessage($devMessage)
    {
        $this->devMessage = $devMessage;

        return $this;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @param string $error
     *
     * @return $this
     */
    public function setError($error)
    {
        $this->error = $error;

        return $this;
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @param int $errorCode
     *
     * @return $this
     */
    public function setErrorCode($errorCode)
    {
        $this->errorCode = $errorCode;

        return $this;
    }

    /**
     * @return string
     */
    public function getUserMessage()
    {
        return $this->userMessage;
    }

    /**
     * @param string $userMessage
     *
     * @return $this
     */
    public function setUserMessage($userMessage)
    {
        $this->userMessage = $userMessage;

        return $this;
    }

    /**
     * @return string
     */
    public function getMore()
    {
        return $this->more;
    }

    /**
     * @param string $more
     *
     * @return $this
     */
    public function setMore($more)
    {
        $this->more = $more;

        return $this;
    }

    /**
     * Error constructor.
     *
     */
    public function __construct($error, $userMessage, $code = 0, $devMessage = '', $more = null)
    {
        $this->error       = $error;
        $this->userMessage = $userMessage;
        $this->more        = $more;
        $this->errorCode   = $code;

        if (application()->getMode() !== 'production')
        {
            $this->devMessage = $devMessage;
        }
    }
}
