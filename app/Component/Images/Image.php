<?php

namespace DS\Component\Images;

use DS\Application;
use Phalcon\Di\AbstractInjectionAware;
use Phalcon\Exception;

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
class Image
    extends AbstractInjectionAware
{
    /**
     * @var string
     */
    protected $srcFile = '';
    
    /**
     * @var string
     */
    protected $tmpFile = '';
    
    /**
     * @var string
     */
    private $cacheDirectory = '/tmp';
    
    /**
     * Convert image to png
     *
     * @param int $maxSize
     *
     * @return string
     * @throws Exception
     */
    public function convertToPng(int $maxSize = 100): Image
    {
        list($width_orig, $height_orig, $type) = getimagesize($this->srcFile);
        
        // Get the aspect ratio
        $ratio_orig = $width_orig / $height_orig;
        
        $width  = $maxSize;
        $height = $maxSize;
        
        // resize to height (original is portrait)
        if ($ratio_orig < 1)
        {
            $width = $height * $ratio_orig;
        }
        // resize to width (original is landscape)
        else
        {
            $height = $width / $ratio_orig;
        }
        
        switch ($type)
        {
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($this->srcFile);
                break;
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($this->srcFile);
                break;
            case IMAGETYPE_PNG:
                // Do nothing if file is already png
                $this->tmpFile = $this->srcFile;
                
                return $this;
                
                break;
            default:
                throw new Exception('Unrecognized image type ' . $type);
        }
        
        // create a new blank image
        $newImage = imagecreatetruecolor($width, $height);
        
        // Copy the old image to the new image
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $width, $height, $width_orig, $height_orig);
        
        // Output to a temp file
        $destFile = tempnam($this->cacheDirectory, '');
        imagepng($newImage, $destFile);
        
        // Free memory
        imagedestroy($newImage);
        
        if (is_file($destFile))
        {
            $this->tmpFile = $destFile;
            
            return $this;
        }
        
        throw new Exception('Image conversion failed.');
    }
    
    /**
     * @return string
     */
    public function getTmpFile(): string
    {
        return $this->tmpFile;
    }
    
    /**
     * @return string
     */
    public function getSrcFile(): string
    {
        return $this->srcFile;
    }
    
    /**
     * @param string $srcFile
     *
     * @return $this
     */
    public function setSrcFile($srcFile)
    {
        $this->srcFile = $srcFile;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getContent(): string
    {
        $f    = fopen($this->tmpFile, 'rb');
        $data = fread($f, 255);
        fclose($f);
        
        return $data;
    }
    
    /**
     * @return Image
     */
    public function removeTmpFile(): Image
    {
        // Remove the tempfile
        if (is_file($this->tmpFile))
        {
            unlink($this->tmpFile);
        }
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getCacheDirectory(): string
    {
        return $this->cacheDirectory;
    }
    
    /**
     * @param string $cacheDirectory
     *
     * @return $this
     */
    public function setCacheDirectory($cacheDirectory)
    {
        $this->cacheDirectory = $cacheDirectory;
        
        return $this;
    }
    
    /**
     * Image constructor.
     *
     * @param string $srcFile
     *
     * @throws Exception
     */
    public function __construct(string $srcFile)
    {
        $this->srcFile        = $srcFile;
        $this->cacheDirectory = Application::instance()->getRootDirectory() . '/system/cache/';
    }
    
    /**
     * Remove temp file on destruction
     */
    public function __destruct()
    {
        $this->removeTmpFile();
    }
}
