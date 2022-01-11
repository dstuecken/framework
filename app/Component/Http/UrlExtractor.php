<?php

namespace DS\Component\Http;

/**
 * DS-Framework
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component\Http
 */
class UrlExtractor extends \rollbackpt\UrlExtractor\UrlExtractor
{
    /**
     * @var string
     */
    private $urlContent = '';
    
    /**
     * @return string
     */
    public function getUrlContent(): string
    {
        return $this->urlContent;
    }
    
    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
    
    /**
     * Function checkImageUrl
     *
     * Utility function used by getImages to check image URL
     * and complete relative URLs
     *
     * @param string $url Url of the image to be checked
     *
     * @return string Image url
     */
    protected function checkImageUrl($url)
    {
        $pattern  = '/^[^(\.|\/)].*?[\.?].*?(.jpg|.gif|.png|.jpeg|.bmp)/i';
        $pattern2 = '/(.jpg|.gif|.png|.jpeg|.bmp)$/i';
        
        $url = preg_replace('/\.\.\//', '', $url);
        
        if (!preg_match($pattern, $url))
        {
            if (preg_match($pattern2, $url))
            {
                if ($url[1] !== '/')
                {
                    return ($url[0] === '/') ? $this->host . $url : $this->host . '/' . $url;
                }
            }
        }
        
        return $url;
    }
    
    /**
     * Function getText
     *
     * Utility function that extract text between start and end points
     *
     * @param string $text
     * @param string $start
     * @param string $end
     *
     * @return string The text extracted
     */
    protected function getText($text, $start, $end)
    {
        $a = explode($start, $text);
        if (isset($a[1]))
        {
            $b = explode($end, $a[1]);
            
            return $b[0];
        }
        
        return '';
    }
    
    /**
     * Function curlGetContents
     *
     * Same as file_get_contents but using curl to avoid getting error 403
     * forbidden because the request doesn't have a valid user agent
     *
     * @param string $url Url to get the contents from using Curl
     *
     * @return string $output Contents obtained from the url
     */
    protected function curlGetContents($url)
    {
        // create curl resource
        $ch = curl_init();
        
        // set url
        curl_setopt($ch, CURLOPT_URL, $url);
        
        //return the transfer as a string
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');
        curl_setopt($ch, CURLOPT_HEADER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        // $output contains the output string
        $this->urlContent = curl_exec($ch);
        $this->url        = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        
        // close curl resource to free up system resources
        curl_close($ch);
        
        return $this->urlContent;
    }
}
