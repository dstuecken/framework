<?php

namespace DS\Component\Filesystem\Flysystem;

use League\Flysystem\Adapter\Local;

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
class LocalFlysystemAdapter extends Local implements DsFlysystemAdapterInterface
{
    use WebPathTrait, DefaultConfigTrait;

    /**
     * @var string
     */
    private static $physicalPath = '';

    /**
     * @param array $config
     *
     * @return LocalFlysystemAdapter
     */
    public static function instance(array $config): LocalFlysystemAdapter
    {
        LocalFlysystemAdapter::$physicalPath = $config['path'];
        LocalFlysystemAdapter::$webPath      = $config['webpath'];

        return new LocalFlysystemAdapter(LocalFlysystemAdapter::$physicalPath);
    }
}
