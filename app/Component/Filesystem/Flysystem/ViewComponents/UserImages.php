<?php

namespace DS\Component\Filesystem\Flysystem\ViewComponents;

use DS\Component\ServiceManager;

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
class UserImages
{
    /**
     * @param string $userImagePath
     *
     * @return string
     */
    public static function getWebPath(string $userImagePath): string
    {
        return ServiceManager::instance()->getFiles()->getWebPath('user-images/' . $userImagePath);
    }
}
