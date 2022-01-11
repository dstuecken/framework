<?php

namespace DS\Exceptions;

use Phalcon\Exception;

/**
 * DS-Framework
 *
 * Security Exception (Password/Login errors)
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Controller
 */
class PermissionDeniedException extends Exception
{
    protected $userRoleThatFailed;

    /**
     * PermissionDeniedException constructor.
     *
     * @param string         $userRoleThatFailed
     * @param string         $message
     * @param string         $field
     * @param int            $code
     * @param \Throwable|null $previous
     */
    public function __construct($userRoleThatFailed, string $message = "", string $field = '', int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);

        $this->userRoleThatFailed = $userRoleThatFailed;
    }
}
