<?php

namespace DS\Component\Filesystem\Flysystem;

use League\Flysystem\Cached\CachedAdapter as FlysystemCachedAdapter;
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
class Filesystem extends \League\Flysystem\Filesystem
{

    /**
     * @var CachedAdapter
     */
    protected $adapter;

    /**
     * @param string $forFile
     *
     * @return string
     */
    public function getDefaultConfig(): Config
    {
        return $this->adapter->getDefaultConfig();
    }

    /**
     * @param string $forFile
     *
     * @return string
     */
    public function getWebPath(string $forFile = ''): string
    {
        return $this->adapter->getWebPath($forFile);
    }
}
