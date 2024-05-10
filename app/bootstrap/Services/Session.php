<?php

if (!defined('FRAMEWORK_DEFAULT_SESSION_LIFETIME'))
{
    define('FRAMEWORK_DEFAULT_SESSION_LIFETIME', 2592000);
}

if (!defined('FRAMEWORK_DEFAULT_SESSION_PREFIX'))
{
    define('FRAMEWORK_DEFAULT_SESSION_PREFIX', 'php.session.');
}

if (!defined('FRAMEWORK_DEFAULT_SESSION_PERSISTENT'))
{
    define('FRAMEWORK_DEFAULT_SESSION_PERSISTENT', true);
}

if (!defined('FRAMEWORK_DEFAULT_SESSION_SERIALIZER'))
{
    define('FRAMEWORK_DEFAULT_SESSION_SERIALIZER', 'Json');
}

if (!defined('FRAMEWORK_DEFAULT_SESSION_SAVE_PATH'))
{
    define('FRAMEWORK_DEFAULT_SESSION_SAVE_PATH', '/tmp');
}

/**
 * DS-Framework
 *
 * Session Initialization
 *
 * @package DS
 * @version $Version$
 */
return function (\DS\Interfaces\GeneralApplication $application, Phalcon\Di\FactoryDefault $di)
{

    // Register 'session'
    $di->setShared(
        'session',
        function () use ($di, $application)
        {
            $config  = $application->getConfig();
            $session = new \DS\Component\Session\Manager();

            if ($config['redis'])
            {
                $redisAdapter = new \DS\Component\Session\Adapter\RedisAdapter(
                    new \Phalcon\Storage\AdapterFactory(new \Phalcon\Storage\SerializerFactory),
                    [
                        'prefix' => $config['session']->prefix ?? FRAMEWORK_DEFAULT_SESSION_PREFIX,
                        'host' => $config['redis']->host ?? 'localhost',
                        'port' => $config['redis']->port ?? 6379,
                        'auth' => $config['redis']->auth ?? null,
                        'persistent' => FRAMEWORK_DEFAULT_SESSION_PERSISTENT,
                        'index' => $config['redis']->index ?? 1,
                        'defaultSerializer' => $config['session']->prefix ?? FRAMEWORK_DEFAULT_SESSION_SERIALIZER,
                        'lifetime' => $config['session']->prefix ?? 'php.session.' ?? FRAMEWORK_DEFAULT_SESSION_LIFETIME,
                    ]
                );

                $session->setAdapter($redisAdapter);
            }
            else
            {
                $files = new \Phalcon\Session\Adapter\Stream(
                    [
                        'savePath' => FRAMEWORK_DEFAULT_SESSION_SAVE_PATH,
                    ]
                );
                $session->setAdapter($files);
            }


            $session->start();

            return $session;
        }
    );
};
