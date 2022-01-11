<?php

namespace DS\Component\Session\Adapter;

use Phalcon\Session\Adapter\Redis;

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
class RedisAdapter extends Redis
{
    
    public function get($id)
    {
        return $this->read($id);
    }
    
    /**
     * @return bool
     */
    public function isStarted(): bool
    {
        return true;
    }
    
    /**
     * @todo implement a session refresh to prevent sessions from timing out.
     * @see  https://forum.phalconphp.com/discussion/13229/update-session-lifetime#C39513
     */
    public function refresh()
    {
    
    }
    
}
