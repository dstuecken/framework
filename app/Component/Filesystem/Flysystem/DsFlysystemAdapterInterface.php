<?php

namespace DS\Component\Filesystem\Flysystem;

use League\Flysystem\Config;
use League\Flysystem\FilesystemAdapter;

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
interface DsFlysystemAdapterInterface extends FilesystemAdapter
{
    /**
     * @param string $forFile
     *
     * @return string
     */
    public function getWebPath(string $forFile = ''): string;
    
    /**
     * @return Config
     */
    public function getDefaultConfig(): Config;
}
