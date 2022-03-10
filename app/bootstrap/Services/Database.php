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
        function () use ($config, $di) {
            
            $config['read-database']['persistent'] = false;
            $config['read-database']['options']    = [
                \PDO::ATTR_PERSISTENT => 0,
            ];
            
            $db = new DbAdapter(
                $config['read-database']
            );
            
            if ($eventsManager = \DS\Component\ServiceManager::instance($di)->getEventsManager())
            {
                $db->setEventsManager($eventsManager);
            }
            
            return $db;
        }
    );
    
    $di->setShared(
        'write-database',
        function () use ($config, $di) {
            
            $config['read-database']['persistent'] = false;
            $config['read-database']['options']    = [
                \PDO::ATTR_PERSISTENT => 0,
            ];
            
            $db = new DbAdapter(
                $config['write-database']
            );
    
            if ($eventsManager = \DS\Component\ServiceManager::instance($di)->getEventsManager())
            {
                $db->setEventsManager($eventsManager);
            }
            
            return $db;
        }
    );
    
    $di['db'] = $di['write-database'];
    
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
