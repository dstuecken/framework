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
return function (\DS\Interfaces\GeneralApplication $application, Phalcon\Di\FactoryDefault $di) {
    /**
     * Setup Crypt service
     *
     * @return Crypt
     */
    $di->setShared(
        'stripe',
        function () use ($application, $di) {
            $config = $application->getConfig();
            
            $stripe = new \Stripe\Stripe();
            \Stripe\Stripe::setApiKey($config['stripe']['secret-key']);
            
            return new \DS\Component\Payments\Provider\Stripe\Stripe(
                $stripe,
                $di->get(\DS\Constants\Services::AUTH)->getClient()->getId() ?: ''
            );
        }
    );
    
};
