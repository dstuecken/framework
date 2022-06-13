<?php

/**
 * DS-Framework
 *
 * Sentry Initialization
 *
 * @package DS
 * @version $Version$
 */
return function (\DS\Interfaces\GeneralApplication $application, Phalcon\Di\FactoryDefault $di) {
    $di->setShared(
        \DS\Constants\Services::RAVENCLIENT,
        function () use ($application, $di) {
            $config = $application->getConfig()->toArray();
            
            $options = [
                'dsn' => $config['sentry']['dsn'] ?: null,
                'environment' => $config['mode'],
                'release' => isset($config['version']) ? $config['version'] : '',
            ];
            \Sentry\init($options);
            
            return new \Sentry\Client($options);
        }
    );
    
};
