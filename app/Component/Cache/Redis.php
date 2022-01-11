<?php

namespace DS\Component\Cache;

use Phalcon\Storage\SerializerFactory;

/**
 * DS-Framework
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component
 */
class Redis
    extends Provider
{
    
    /**
     * @var mixed|string
     */
    private $cachePrefix = '';
    
    /**
     * @var string
     */
    private $namespace = '';
    
    /**
     * Returns a cached content
     *
     * @param string $key
     * @param mixed $defaultValue
     *
     * @return mixed
     * @throws \Phalcon\Storage\Exception
     */
    public function get(string $key, $defaultValue = null)
    {
        return $this->backend->get($this->namespace . '.' . $key, $defaultValue);
    }
    
    /**
     * Stores cached content into the file backend and stops the frontend
     *
     * @param int|string $keyName
     * @param string     $content
     * @param int        $lifetime
     *
     * @return $this
     * @throws \Phalcon\Storage\Exception
     */
    public function set($key, $value = null, $lifetime = null)
    {
        $this->backend->set($this->namespace . '.' . $key, $value, $lifetime);
        
        return $this;
    }
    
    /**
     * Invalidate cache with a prefix
     *
     * @param string $prefix
     *
     * @return array
     * @throws \Phalcon\Storage\Exception
     */
    public function invalidate(string $prefix = ''): array
    {
        $keys = $this->backend->getKeys($this->cachePrefix . $this->namespace . ($prefix !== '' ? '.' . $prefix : ''));
        
        foreach ($keys as $key)
        {
            $this->backend->delete($key);
        }
        
        return $keys;
    }
    
    /**
     * Flush whole cache
     *
     * @throws \Phalcon\Storage\Exception
     */
    public function flush(): Redis
    {
        $this->backend->clear();
        
        return $this;
    }
    
    /**
     * @param string $namespace
     *
     * @return $this
     */
    public function setNamespace(string $namespace): Redis
    {
        $this->namespace = $namespace;
        
        return $this;
    }
    
    /**
     * Redis constructor.
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        $serializerFactory = new SerializerFactory();
        $this->backend     = new \Phalcon\Cache\Adapter\Redis($serializerFactory, $options);
        
        $this->cachePrefix = isset($options['prefix']) ? $options['prefix'] : '';
    }
}
