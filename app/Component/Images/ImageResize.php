<?php

namespace DS\Component\Images;

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
class ImageResize
    extends Image
{
    /**
     * @param string $imageFilename
     * @param int    $height
     *
     * @return \Gumlet\ImageResize
     * @throws \Gumlet\ImageResizeException
     */
    public static function resize(string $imageFilename, int $height = 250): \Gumlet\ImageResize
    {
        // Resize image to $width px width
        $imageFilename              = new \Gumlet\ImageResize($imageFilename);
        $imageFilename->quality_jpg = 85;
        $imageFilename->quality_png = 7;
        
        $imageFilename->resizeToHeight($height);
        
        return $imageFilename;
    }
    
    /**
     * @param $image
     *
     * @return \Gumlet\ImageResize
     * @throws \Gumlet\ImageResizeException
     */
    public static function image($image): \Gumlet\ImageResize
    {
        return new \Gumlet\ImageResize($image);
    }
}
