<?php

namespace DS\Component\Filesystem\Flysystem;

use League\Flysystem\AdapterInterface;
use League\Flysystem\Cached\CachedAdapter as FlysystemCachedAdapter;
use League\Flysystem\Cached\CacheInterface;
use League\Flysystem\Config;

/**
 * DS-Framework
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component\Filesystem
 */
class CachedAdapter extends FlysystemCachedAdapter
{

    /**
     * @var DsFlysystemAdapterInterface
     */
    protected $adapter;

    /**
     * @var CacheInterface
     */
    private $cache;

    /**
     * Get the underlying Adapter implementation.
     *
     * @return DsFlysystemAdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param string $forFile
     *
     * @return string
     */
    public function getDefaultConfig(): Config
    {
        return $this->getAdapter()->getDefaultConfig();
    }

    /**
     * @param string $forFile
     *
     * @return string
     */
    public function getWebPath(string $forFile = ''): string
    {
        return $this->getAdapter()->getWebPath($forFile);
    }

    /**
     * Constructor.
     *
     * @param AdapterInterface $adapter
     * @param CacheInterface   $cache
     */
    public function __construct(AdapterInterface $adapter, CacheInterface $cache)
    {
        $this->adapter = $adapter;
        $this->cache = $cache;
        $this->cache->load();
    }
}
