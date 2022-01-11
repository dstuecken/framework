<?php

namespace DS\Component\Cache;

use Phalcon\Cache\Adapter\AdapterInterface;

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
abstract class Provider
{
    
    /**
     * @var AdapterInterface
     */
    protected $backend = null;
    
    /**
     * @return AdapterInterface
     */
    public function getBackend()
    {
        return $this->backend;
    }
    
    /**
     * @param $key
     *
     * @return bool
     */
    public function exists($key)
    {
        return $this->backend->has($key);
    }
    
    /**
     * @param $key
     *
     * @return bool
     */
    public function has($key)
    {
        return $this->backend->has($key);
    }
    
    /**
     * Returns a cached content
     *
     * @param int|string $key
     * @param mixed     $defaultValue
     *
     * @return mixed
     */
    public function get(string $key, $defaultValue = null)
    {
        return $this->backend->get($key, $defaultValue);
    }
    
    /**
     * Stores cached content into the file backend and stops the frontend
     *
     * @param int|string $keyName
     * @param string     $content
     * @param int        $lifetime
     *
     * @return $this
     */
    public function set($key, $value = null, $lifetime = null)
    {
        $this->backend->set($key, $value, $lifetime);
        
        return $this;
    }
    
}
