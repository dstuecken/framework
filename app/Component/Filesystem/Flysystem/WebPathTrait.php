<?php

namespace DS\Component\Filesystem\Flysystem;

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
trait WebPathTrait
{

    /**
     * @var string
     */
    private static $webPath = '';

    /**
     * @param string $forFile
     *
     * @return string
     */
    public function getWebPath(string $forFile = ''): string
    {
        return str_replace('%RAND%', mt_rand(1,9), self::$webPath) . $forFile;
    }
}
