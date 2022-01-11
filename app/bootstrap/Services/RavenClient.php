<?php

/**
 * DS-Framework
 *
 * Sentry Initialization
 *
 * @package DS
 * @version $Version$
 */
return function (\DS\Interfaces\GeneralApplication $application, Phalcon\Di\FactoryDefault $di)
{
    /**
     * Setup Raven Client (for sentry.io)
     *
     * @return
     */
    $di->setShared(
        \DS\Constants\Services::RAVENCLIENT,
        function () use ($application, $di): Raven_Client
        {
            $config = $application->getConfig()->toArray();
            
            $client = (new Raven_Client($config['sentry']['key'] ?: null, [
                'environment' => $config['mode'],
                'release' => isset($config['version']) ? $config['version'] : '',
            ]));
            
            $client->user_context([
                'id' => $di->get(\DS\Constants\Services::AUTH)->getUserId(),
            ]);
            
            if ($config['mode'] !== 'development') {
                $client->install();
            }
            
            return $client;
        }
    );

};
