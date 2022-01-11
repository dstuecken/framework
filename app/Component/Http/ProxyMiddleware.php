<?php

namespace DS\Component\Http;

use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use Orangesoft\Throttler\ThrottlerInterface;

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
class ProxyMiddleware
{
    private $throttler;
    
    public function __construct(ThrottlerInterface $throttler)
    {
        $this->throttler = $throttler;
    }
    
    public function __invoke(callable $handler): \Closure
    {
        return function (Request $request, array $options) use ($handler) {
            $node = $this->throttler->next();
            
            $options[RequestOptions::PROXY] = $node->getName();
            
            return $handler($request, $options);
        };
    }
}
