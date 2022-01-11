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
        'crypt',
        function () use ($application)
        {
            $config = $application->getConfig();

            $crypt = new Crypt();
            $crypt->setKey($config['crypt']['key']);

            return $crypt;
        }
    );

};