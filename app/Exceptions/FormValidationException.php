<?php
namespace DS\Exceptions;

use Phalcon\Exception;

/**
 * DS-Framework
 *
 * FormValidationException
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Controller
 */
class FormValidationException extends Exception
{
    private $userErrors = [];

    /**
     * @return array
     */
    public function getUserErrors(): array
    {
        return $this->userErrors;
    }

    /**
     * FormValidationException constructor.
     *
     * @param string $message
     * @param array  $userErrors
     */
    public function __construct($message, array $userErrors = [])
    {
        parent::__construct($message);
    }
}
