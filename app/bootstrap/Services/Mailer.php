<?php

/**
 * DS-Framework
 *
 * Mailer Initialization
 *
 * @package DS
 * @version $Version$
 */
return function (\DS\Interfaces\GeneralApplication $application, Phalcon\Di\FactoryDefault $di)
{
    /**
     * Setup Mail Manager
     *
     * @return \DS\Component\Mail\MailManager
     */
    $di->setShared(
        \DS\Constants\Services::MAILER,
        function () use ($application)
        {
            $config = $application->getConfig()->toArray();

            return new \DS\Component\Mail\MailManager($config['mail']);
        }
    );
    
    /**
     * Setup SendGrid
     *
     * @return DS\Component\ProviderLogic\Api\SendGrid\SendGrid
     */
    $di->setShared(
        'sendgrid',
        function () use ($application)
        {
            $config = $application->getConfig()->toArray();
            return new DS\Component\ProviderLogic\Api\SendGrid\SendGrid($config['mail']['sendgrid']['apikey']);
        }
    );
    
    

};
