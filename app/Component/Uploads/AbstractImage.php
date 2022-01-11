<?php

namespace DS\Component\Uploads;

use DS\Component\Filesystem\Flysystem\DsFlysystemAdapterInterface;
use DS\Component\Images\Extensions;
use DS\Component\Images\ImageResize;
use Phalcon\Http\Client\Provider\Curl;
use Phalcon\Logger;

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
 */
abstract class AbstractImage
{
    /**
     * @var DsFlysystemAdapterInterface
     */
    protected $files;
    
    /**
     * @var string
     */
    protected $imageDir = 'images/';
    
    /**
     * @var string
     */
    protected $imagePrefix = '';
    
    /**
     * @var int
     */
    protected $minimumHeight = 250;
    
    /**
     * @var int
     */
    protected $minimumWidth = 250;
    
    /**
     * Resize sizes
     *
     * @var int[]
     */
    protected $resizes = [
        665,
        200,
        100,
        80,
    ];
    
    /**
     * @var string
     */
    protected $imageTitle = '';
    
    /**
     * @return string
     */
    public function getImageDir()
    {
        return $this->imageDir;
    }
    
    /**
     * @param string $imageTitle
     *
     * @return $this
     */
    public function setImageTitle($imageTitle)
    {
        $this->imageTitle = $imageTitle;
        
        return $this;
    }
    
    /**
     * @param string $imageUrl
     *
     * @return string
     * @throws \Exception
     */
    public function execute(string $imageUrl): string
    {
        try
        {
            if (strpos($imageUrl, 'http:///') === 0)
            {
                return '';
            }
            
            if (strpos($imageUrl, 'http') !== 0)
            {
                return '';
            }
            
            $curl = new Curl();
            $curl->setOptions(
                [
                    CURLOPT_HEADER => 0,
                    CURLOPT_FOLLOWLOCATION => 1,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                ]
            );
            $curlResponse  = $curl->get($imageUrl);
            $imageContents = $curlResponse->body;
            
            $imageData = 'data://' . $curlResponse->header->get('Content-Type') . ';base64,' . base64_encode($imageContents);
            
            // Resize to 900 which will be the default image size
            $imageName = $this->writeImage(
                ImageResize::resize($imageData, 900),
                $imageUrl
            );
            
            // Resize to other sizes
            foreach ($this->resizes as $size)
            {
                // Resize to 665
                $this->writeImage(
                    ImageResize::resize($imageData, $size),
                    $imageUrl,
                    $size . '/'
                );
            }
            
            return $imageName;
        }
        catch (\Exception $e)
        {
            application()->log('Image download error: ' . $e->getMessage(), Logger::ERROR);
        }
        
        /*
        throw new DimensionException(
            'Image was not downloaded since it only has a dimension of ' . $image->getSourceWidth() . 'x' . $image->getSourceHeight(
            ) . '. Minimum dimension is ' . $this->minimumWidth . 'x' . $this->minimumHeight . '.'
        );
        */
        
        return '';
    }
    
    /**
     * @param string $imageName
     *
     * @return string
     */
    protected function hashImageName(string $imageName): string
    {
        if ($this->imageTitle !== '')
        {
            return md5($this->imageTitle);
        }
        
        return md5($imageName);
    }
    
    /**
     * @param \Gumlet\ImageResize $image
     * @param string              $imageNameForMd5Hash
     * @param string              $pathPrefix
     *
     * @return string
     */
    public function writeImage(\Gumlet\ImageResize $image, string $imageNameForMd5Hash, $pathPrefix = '')
    {
        if ($this->validate($image))
        {
            $imageName = $this->imagePrefix . $this->hashImageName($imageNameForMd5Hash) . '.' . Extensions::extensionBySourceType($image->source_type);
            
            // Write to flysystem
            if ($this->files->has($this->imageDir . $pathPrefix . $imageName))
            {
                $this->files->delete($this->imageDir . $pathPrefix . $imageName);
            }
            
            $this->files->write($this->imageDir . $pathPrefix . $imageName, $image->getImageAsString(), $this->files->getDefaultConfig());
            
            return $imageName;
        }
        
        return '';
    }
    
    /**
     * @param \Gumlet\ImageResize $image
     *
     * @return bool
     */
    protected function validate(\Gumlet\ImageResize $image): bool
    {
        if ($image->getSourceHeight() >= $this->minimumHeight && $image->getSourceWidth() >= $this->minimumWidth)
        {
            return true;
        }
        
        return false;
    }
    
    /**
     * AbstractImage constructor.
     *
     * @param DsFlysystemAdapterInterface $files
     */
    public function __construct(DsFlysystemAdapterInterface $files)
    {
        $this->files = $files;
    }
}
