<?php

namespace DS\Component\Images;

use DS\Application;
use DS\Component\ServiceManager;
use Gumlet\ImageResizeException;
use Phalcon\Di\AbstractInjectionAware;
use Phalcon\Exception;
use Phalcon\Http\Client\Provider\Curl;

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
class Download
    extends AbstractInjectionAware
{
    /**
     * @param string $directory
     * @param string $imageUrl
     * @param string $name
     * @param int    $resizeTo
     *
     * @return string
     * @throws Exception
     */
    public static function downloadInto(string $directory, string $imageUrl, string $name, $resizeTo = 0): string
    {
        $md5Name = md5($name);
        // Write to flysystem
        $files = ServiceManager::instance()->getFiles();
        
        try
        {
            $curl = new Curl();
            $curl->setOptions(
                [
                    CURLOPT_HEADER => 0,
                    CURLOPT_TIMEOUT => 5,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_HEADER => 0,
                    CURLOPT_FOLLOWLOCATION => 1,
                    CURLOPT_SSL_VERIFYHOST => 0,
                    CURLOPT_SSL_VERIFYPEER => 0,
                ]
            );
            $curlResponse  = $curl->get($imageUrl);
            $imageContents = $curlResponse->body;
            
            // Resize
            if (isset($curlResponse) && strstr($curlResponse->header->get('Content-Type'), 'image'))
            {
                try
                {
                    if ($resizeTo > 25)
                    {
                        $image = ImageResize::resize('data://' . $curlResponse->header->get('Content-Type') . ';base64,' . base64_encode($imageContents), 400);
                    }
                    else
                    {
                        $image = ImageResize::image('data://' . $curlResponse->header->get('Content-Type') . ';base64,' . base64_encode($imageContents));
                    }
                }
                catch (ImageResizeException $e)
                {
                    // Failed to resize image.
                    $image = '';
                }
                
                // Write image to post
                $imageName = $md5Name . '.' . Extensions::extensionBySourceType($image->source_type);
                $imagePath = $directory . '/' . $imageName;
                
                if ($files->has($imagePath))
                {
                    $files->delete($imagePath);
                }
                $files->write($imagePath, $image->getImageAsString(), $files->getDefaultConfig());
                
                return $imageName;
            }
            else
            {
                if ($files->has($directory . '/' . $md5Name . '.png'))
                {
                    return $md5Name . '.png';
                }
                elseif ($files->has($directory . '/' . $md5Name . '.jpeg'))
                {
                    return $md5Name . '.jpeg';
                }
            }
        }
        catch (Exception $e)
        {
            // General error
            Application::instance()->log('Download::downloadInto: ' . $e->getMessage());
        }
        catch (\Phalcon\Http\Client\Exception $e)
        {
            // Http error
            Application::instance()->log('Download::downloadInto: ' . $e->getMessage());
        }
        
        return '';
    }
}
