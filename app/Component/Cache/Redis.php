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
    protected $cachePrefix = '';


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
        return $this->backend->get($key, $defaultValue);
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
        $this->backend->set($key, $value, $lifetime);

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
        $keys = $this->backend->getKeys($this->cachePrefix . ($prefix !== '' ? '.' . $prefix : ''));

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

    public function getPrefix(): string
    {
        return $this->backend->getPrefix();
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
