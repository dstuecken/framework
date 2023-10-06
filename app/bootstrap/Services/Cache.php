<?php

/**
 * DS-Framework
 *
 * AsstesManager Initialization
 *
 * @package DS
 * @version $Version$
 */
return function (\DS\Interfaces\GeneralApplication $application, Phalcon\Di\FactoryDefault $di) {
    
    $config = $application->getConfig()->toArray();
    
    /**
     * Register memcache service
     
    $di->setShared(
        \DS\Constants\Services::MEMCACHE,
        function () use ($config) {
            
            if (!isset($config['memcache']))
            {
                throw new \Phalcon\Exception('Error. Memcache service is used but there is no configuration.');
            }
            
            return new \DS\Component\Cache\Memcache(
                $config['memcache'],
                "fw.cache." . DSFW_VERSION . "."
            );
        }
    );
     */
    
    /**
     * Register redis caching service
     */
    $di->setShared(
        \DS\Constants\Services::REDIS,
        function () use ($config) {
            
            if (!isset($config['redis']))
            {
                throw new \Phalcon\Exception('Error. Invalid redis configuration.');
            }
            
            return new \DS\Component\Cache\Redis(
                [
                    "lifetime" => \DS\Model\Helper\Seconds::DaysOne,
                    "prefix" => $config['redis']['prefix'] ?: "fw.cache." . APP_VERSION . ".",
                    'host' => $config['redis']['host'] ?: 'localhost',
                    'port' => $config['redis']['port'],
                    'index' => $config['redis']['index'] ?: null,
                    'auth' => $config['redis']['auth'] ?: null,
                    "persistent" => true,
                ]
            );
        }
    );
    
    /**
     * Register the view cache
     */
    $di->setShared(
        'viewCache',
        function () use ($config, $di) {
            return \DS\Component\ServiceManager::instance($di)->getRedis()->getBackend();
        }
    );
    
    /**
     * Register custom cached model manager
     */
    $di->setShared(
        \DS\Constants\Services::MODELSMANAGER,
        function () use ($di) {
            return new \DS\Model\Manager\CachedReusableModelsManager($di);
        }
    );
    
    /**
     * Register model meta data caching
     */
    $di->setShared(
        \DS\Constants\Services::MODELSMETADATA,
        function () use ($config, $di, $application) {
            $serializerFactory = new \Phalcon\Storage\SerializerFactory();
            $adapterFactory    = new \Phalcon\Cache\AdapterFactory($serializerFactory);
            
            if ($config['redis'])
            {
                $cache = new \Phalcon\Mvc\Model\MetaData\Redis(
                    $adapterFactory,
                    [
                        'host' => $config['redis']['host'] ?: 'localhost',
                        'port' => $config['redis']['port'],
                        'auth' => $config['redis']['auth'] ?: null,
                        "lifetime" => \DS\Model\Helper\Seconds::WeeksOne,
                        "prefix" => $config['redis']['prefix'] ?: "fw." . APP_VERSION . ".metadata.",
                        'index' => 1,
                    ]
                );
            }
            else
            {
                throw new Exception("Redis config not defined.");
            }
            
            // After model change do a reset once:
            //$cache->reset();
            
            return $cache;
        }
    );
};
