<?php

namespace DS\Exceptions;

use DS\Model\User;
use Phalcon\Exception;

/**
 * https://www.dvlpr.de
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright https://www.dvlpr.de
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Controller
 */
class UserNoAccessException extends Exception
{
    /**
     * @var User
     */
    private $user;
    
    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }
    
    /**
     * UserNoAccessException constructor.
     *
     * @param      $message
     * @param User $user
     */
    public function __construct($message, User $user)
    {
        parent::__construct($message);
        
        $this->user = $user;
    }
}
