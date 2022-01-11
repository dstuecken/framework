<?php

namespace DS\Component\Security;

use DS\Component\Links\HomeLink;

/**
 * DS
 *
 * @copyright 2017 | Dennis Stücken
 *
 * @version   $Version$
 * @package   DS\Component
 */
class ConfirmationToken
{
    /**
     * @return string
     */
    public static function generate()
    {
        return preg_replace('/[^a-zA-Z0-9]/', '', base64_encode(openssl_random_pseudo_bytes(24)));
    }
    
    /**
     * @param string $forToken
     * @param string $prefix
     *
     * @return string
     */
    public static function getUrl(string $forToken, string $prefix = '/login/email?token=')
    {
        return HomeLink::get(sprintf($prefix . '%s', $forToken));
    }
}
