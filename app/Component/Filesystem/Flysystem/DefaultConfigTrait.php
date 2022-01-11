<?php

namespace DS\Component\Filesystem\Flysystem;

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
trait DefaultConfigTrait
{

    /**
     * @var Config
     */
    private static $config;

    /**
     * @param string $forFile
     *
     * @return string
     */
    public function getDefaultConfig(): Config
    {
        if (!self::$config)
        {
            self::$config = new Config();
        }

        return self::$config;
    }

    /**
     * @param Config $config
     *
     * @return $this
     */
    public function setDefaultConfig(Config $config): DsFlysystemAdapterInterface
    {
        self::$config = $config;

        return $this;
    }
}
