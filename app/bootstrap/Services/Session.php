<?php

/**
 * DS-Framework
 *
 * Session Initialization
 *
 * @package DS
 * @version $Version$
 */
return function (\DS\Interfaces\GeneralApplication $application, Phalcon\Di\FactoryDefault $di) {
    
    /*
    $di->setShared(
        'session',
        function () use ($di, $application) {
            $session = new \DS\Component\Session\Manager();
            $files   = new Stream(
                [
                    'savePath' => '/tmp',
                ]
            );
            $session->setAdapter($files);
            $session->start();
            
            return $session;
        }
    );
*/
    
    // Register 'session'
    $di->setShared(
        'session',
        function () use ($di, $application) {
            $session = new \DS\Component\Session\Manager();
            
            /*
            *     'prefix' => 'sess-redis-',
            *     'host' => '127.0.0.1',
            *     'port' => 6379,
            *     'index' => 0,
            *     'persistent' => false,
            *     'auth' => '',
            *     'socket' => ''
            */
            $config       = $application->getConfig();
            $redisAdapter = new \DS\Component\Session\Adapter\RedisAdapter(
                new \Phalcon\Storage\AdapterFactory(new \Phalcon\Storage\SerializerFactory),
                [
                    'prefix' => 'fw.session.',
                    'host' => $config['redis']->host,
                    'port' => $config['redis']->port,
                    'persistent' => true,
                    'index' => 1,
                ]
            );
            
            $session->setAdapter($redisAdapter);
            $session->start();
            
            return $session;
        }
    );
};
