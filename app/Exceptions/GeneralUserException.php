<?php

namespace DS\Exceptions;

use Phalcon\Exception;

/**
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
class GeneralUserException extends Exception
{
    /**
     * Severities
     */
    const high = 0;
    const normal = 1;
    const low = 2;
    const ultralow = 3;
    
    private $severity;
    
    /**
     * @return mixed
     */
    public function getSeverity()
    {
        return $this->severity;
    }
    
    /**
     * @param mixed $severity
     *
     * @return $this
     */
    public function setSeverity($severity)
    {
        $this->severity = $severity;
        
        return $this;
    }
    
    public function __construct($message = "", $severity = self::normal, $code = 0)
    {
        parent::__construct($message, $code);
    }
}
