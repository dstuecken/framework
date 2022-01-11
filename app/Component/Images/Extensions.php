<?php

namespace DS\Component\Images;

use Phalcon\Di\AbstractInjectionAware;

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
 *
 */
class Extensions
    extends AbstractInjectionAware
{
    /**
     * @param $sourceType
     *
     * @return string
     */
    public static function extensionBySourceType($sourceType): string
    {
        $extension = 'png';
        switch ($sourceType)
        {
            case IMAGETYPE_GIF:
                $extension = 'gif';
                break;
            case IMAGETYPE_JPEG:
            case IMAGETYPE_JPEG2000:
                $extension = 'jpeg';
                break;
            case IMAGETYPE_TIFF_II:
            case IMAGETYPE_TIFF_MM:
                $extension = 'tiff';
                break;
            case IMAGETYPE_WEBP:
                $extension = 'webp';
                break;
        }

        return $extension;
    }
}
