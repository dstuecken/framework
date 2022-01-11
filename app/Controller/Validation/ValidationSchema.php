<?php

namespace DS\Controller\Validation;

use Phalcon\Validation;

/**
 * Dennis Stücken
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Controller\Validation
 */
interface ValidationSchema
{
    /**
     * @return Validation
     */
    public static function getSchema();

    /**
     * Validate a set of data according to a set of rules
     *
     * @param array|object $data
     * @param object $entity
     * @return \Phalcon\Validation\Message\Group
     */
    public function validate($data = null, $entity = null);
}

/**
 * Example:
 *
 
 class LoginValidation
     extends Validation
     implements ValidationSchema
     public static function getSchema()
     {
         return (new self())
             ->add(
                 "email",
                 new Validation\Validator\PresenceOf(
                     [
                         "message" => "Please provide a username.",
                     ]
                 )
             )
             ->add(
                 "email",
                 new Validation\Validator\Email(
                     [
                         "message" => "Please provide a valid email address.",
                     ]
                 )
             )
             ->add(
                 "email",
                 new Validation\Validator\StringLength(
                     [
                         "max" => 50,
                         "min" => 5,
                         "messageMaximum" => "Username is too long.",
                         "messageMinimum" => "Please provide a username with a minimum of 5 characters.",
                     ]
                 )
             )
             ->add(
                 "password",
                 new Validation\Validator\PresenceOf(
                     [
                         "message" => "Please provide a password.",
                     ]
                 )
             );
 
     }
 }
 */
