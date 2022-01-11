<?php
/**
 * DS-Framework
 *
 * DB Service Initialization
 *
 * @package DS
 * @version $Version$
 */

use DS\Component\Phql\SqlDialects\GroupConcatDialect as GroupConcatDialect;
use Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter;
use Phalcon\Events\Event;
use Phalcon\Events\Manager as EventsManager;

/**
 * @param \DS\Interfaces\GeneralApplication $application
 * @param \Phalcon\Di\FactoryDefault        $di
 */
return function (\DS\Interfaces\GeneralApplication $application, Phalcon\Di\FactoryDefault $di) {
    $config = $application->getConfig()->toArray();
    
    $dialect = GroupConcatDialect::register();
    /** @noinspection PhpIllegalStringOffsetInspection */
    $config['read-database']['dialectClass'] = $dialect;
    /** @noinspection PhpIllegalStringOffsetInspection */
    //$config['write-database']['dialectClass'] = $dialect;
    
    // Set the database services
    $di->setShared(
        'read-database',
        function () use ($config) {
            
            $config['read-database']['persistent'] = false;
            $config['read-database']['options']    = [
                \PDO::ATTR_PERSISTENT => 0,
            ];
            
            return new DbAdapter(
                $config['read-database']
            );
        }
    );
    
    $di->setShared(
        'write-database',
        function () use ($config) {
            
            $config['read-database']['persistent'] = false;
            $config['read-database']['options']    = [
                \PDO::ATTR_PERSISTENT => 0,
            ];
            
            return new DbAdapter(
                $config['write-database']
            );
        }
    );
    
    $di['db'] = $di['write-database'];
    
    // Ignore unknown columns to prevent unexpected error messages,
    // as seen on github issue https://github.com/phalcon/cphalcon/issues/1652
    // \Phalcon\Mvc\Model::setup(['ignoreUnknownColumns' => true]);
    
    #ifdef DEBUG
    /** @noinspection PhpIllegalStringOffsetInspection */
    if ($config['read-database']['profile'])
    {
        $di->setShared(
            'profiler',
            function () use ($di) {
                return new \Phalcon\Db\Profiler();
            }
        );
        
        /**
         * @var $profiler \Phalcon\Db\Profiler
         */
        $profiler = $di->get('profiler');
        
        $eventsManager = new EventsManager();
        $eventsManager->attach(
            'db',
            function (Event $event, \Phalcon\Db\Adapter $connection) use ($profiler, $di) {
                if ($event->getType() == 'beforeQuery')
                {
                    // Start a profile with the active connection
                    $profiler->startProfile($connection->getSQLStatement());
                }
                
                if ($event->getType() == 'afterQuery')
                {
                    // Stop the active profile
                    $profiler->stopProfile();
                    
                    $profile = $profiler->getLastProfile();
                    
                    $profileLog = "SQL Statement: " . $profile->getSQLStatement() . " (" . str_replace("\n", "", var_export($connection->getSqlVariables(), true)) . ") ==> ";
                    $profileLog .= "Time: " . $profile->getTotalElapsedSeconds();
                    
                    $di->get(\DS\Constants\Services::LOGGER)->log($profileLog, \Phalcon\Logger::DEBUG);
                }
            }
        );
        
        $di->get('read-database')->setEventsManager($eventsManager);
        $di->get('write-database')->setEventsManager($eventsManager);
    }
    
    #endif
    
    // Set the models cache service
    $di->setShared(
        'modelsCache',
        function () {
            $serializerFactory = new \Phalcon\Storage\SerializerFactory();
            $adapterFactory    = new \Phalcon\Cache\AdapterFactory($serializerFactory);
            
            $options = [
                'defaultSerializer' => 'Php',
                'lifetime' => 7200,
            ];
            
            $adapter = $adapterFactory->newInstance('apcu', $options);
            
            return new \Phalcon\Cache($adapter);
        }
    );
    
    \Phalcon\Mvc\Model::setup(
        [
            'columnRenaming' => true,
            'ignoreUnknownColumns' => true,
            'castOnHydrate' => true,
        ]
    );
};
