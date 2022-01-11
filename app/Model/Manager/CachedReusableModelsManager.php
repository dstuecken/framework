<?php
namespace DS\Model\Manager;

use DS\Constants\Services;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\Model\Manager as ModelManager;

/**
 * DS-Framework
 *
 * Cache reusable models into a cache system like apc, redis or memcache
 *
 * @author Dennis Stücken
 * @license proprietary

 * @copyright Dennis Stücken
 * @link https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Model
 */
class CachedReusableModelsManager
    extends ModelManager
{
    /**
     * @var \Phalcon\Cache\Adapter\AdapterInterface
     */
    public $cache = null;

    /**
     * Returns a reusable object from the cache
     *
     * @param string $modelName
     * @param string $key
     *
     * @return object
     */
    public function getReusableRecords(string $modelName, string $key)
    {
        if ($this->cache->has($key))
        {
            return $this->cache->get($key);
        }

        // For the rest, use the memory cache
        return parent::getReusableRecords($modelName, $key);
    }

    /**
     * Stores a reusable record in the cache
     *
     * @param string $modelName
     * @param string $key
     * @param mixed  $records
     */
    public function setReusableRecords(string $modelName, string $key, $records): void
    {
        $this->cache->set($key, $records);

        parent::setReusableRecords($modelName, $key, $records);
    }

    /**
     * Construct
     *
     * @param FactoryDefault $di
     */
    public function __construct(FactoryDefault $di)
    {
        $this->setDI($di);
        $this->cache = $di->get(Services::REDIS);
    }
}
