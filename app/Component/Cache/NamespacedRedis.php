<?php

namespace DS\Component\Cache;

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
class NamespacedRedis
    extends Redis
{
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
        if ($this->namespace)
        {
            $key = $this->namespace . '.' . $key;
        }

        return parent::get($key, $defaultValue);
    }

    /**
     * Stores cached content into the file backend and stops the frontend
     *
     * @param int|string $keyName
     * @param string $content
     * @param int $lifetime
     *
     * @return $this
     * @throws \Phalcon\Storage\Exception
     */
    public function set($key, $value = null, $lifetime = null)
    {
        if ($this->namespace)
        {
            $key = $this->namespace . '.' . $key;
        }

        return parent::set($key, $value, $lifetime);
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
        if ($this->namespace)
        {
            $key = $this->cachePrefix . $this->namespace . ($prefix !== '' ? '.' . $prefix : '');
        }
        else
        {
            $key = $this->cachePrefix . ($prefix !== '' ? '.' . $prefix : '');
        }

        $keys = $this->backend->getKeys($key);

        foreach ($keys as $key)
        {
            $this->backend->delete($key);
        }

        return $keys;
    }

    /**
     * @param string $namespace
     *
     * @return $this
     */
    public function setNamespace(string $namespace): NamespacedRedis
    {
        $this->namespace = $namespace;

        return $this;
    }
}
