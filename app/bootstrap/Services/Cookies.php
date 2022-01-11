<?php
use Phalcon\Crypt;

/**
 * DS-Framework
 *
 * Crypt Initialization
 *
 * @package DS
 * @version $Version$
 */
return function (\DS\Interfaces\GeneralApplication $application, Phalcon\Di\FactoryDefault $di)
{
    /**
     * Setup Crypt service
     *
     * @return Crypt
     */
    $di->setShared(
        \DS\Constants\Services::COOKIES,
        function () use ($application)
        {
            $cookies = new \Phalcon\Http\Response\Cookies();
            $cookies->useEncryption(true);

            return $cookies;
        }
    );

};