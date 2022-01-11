<?php

namespace DS\Component\Http;

use Orangesoft\Throttler\Collection\Node;

/**
 * DS-Framework
 *
 * Override this and add your proxies
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component
 */
class GlobalProxyNodes
{
    /**
     * @var Node[]
     */
    protected $nodes;
    
    /**
     * @return Node[]
     */
    public function getNodes(): array
    {
        return $this->nodes;
    }
    
    /**
     * @return GlobalProxyNodes
     */
    public static function factory(): GlobalProxyNodes
    {
        return new self();
    }
    
    public function __construct()
    {
        $this->nodes = [];
    }
}
