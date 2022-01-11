<?php

namespace DS\Component\Networking;

/**
 * Badges
 *
 * @author            Dennis Stücken
 * @license           proprietary
 * @copyright        https://www.dvlpr.de
 * @link              https://www.dvlpr.de
 *
 * @version           $Version$
 * @package           DS\Component
 *
 */
class IpAddress
{
    public static function getUserIpAddress()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP']))
        {
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
        else
        {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return $ip;
    }
}


