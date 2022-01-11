<?php

namespace DS\Exceptions;

use DS\Application;
use Phalcon\Exception;

/**
 * Coders
 *
 * @copyright 2016/17 | Aspirantic.com
 *
 * @version   $Version$
 * @package   Coders\Controller
 */
class ApiException extends Exception
{
    /**
     * @var string
     */
    protected $devMessage = '';

    /**
     * @var string
     */
    protected $error = '';

    /**
     * @var int
     */
    protected $errorCode = 0;

    /**
     * @var string
     */
    protected $more = '';

    /**
     * @return string
     */
    public function getDevMessage()
    {
        return $this->devMessage;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return int
     */
    public function getErrorCode()
    {
        return $this->errorCode;
    }

    /**
     * @return string
     */
    public function getMore()
    {
        return $this->more;
    }

    /**
     * ApiException constructor.
     *
     * @param string $userError
     * @param string $devMessage
     * @param string $error
     * @param int    $errorCode
     * @param null   $more
     */
    public function __construct($userError, $devMessage = '', $error = '', $errorCode = 0, $more = null)
    {
        parent::__construct($userError);

        $this->message   = $userError;
        $this->error     = $error;
        $this->errorCode = $errorCode;
        $this->more      = $more;

        if (application()->getMode() !== 'production')
        {
            $this->devMessage = $devMessage;
        }
    }
}
