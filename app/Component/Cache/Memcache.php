<?php
namespace DS\Component\Cache;

use Phalcon\Cache\Backend\Libmemcached as MemcacheBackend;
use Phalcon\Cache\Frontend\Data as FrontData;

/**
 * DS-Framework
 *
 * @author Dennis Stücken
 * @license proprietary

 * @copyright Dennis Stücken
 * @link https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component
 */
class Memcache
    extends Provider
{
    /**
     * Memcache constructor.
     *
     * @param array $servers
     */
    public function __construct(array $servers, $prefix = null)
    {
        $this->cache = new FrontData(
            [
                "lifetime" => 86400
            ]
        );

        $this->backend = new MemcacheBackend(
            $this->cache, [
                "servers" => $servers
            ]
        );
    }
}
