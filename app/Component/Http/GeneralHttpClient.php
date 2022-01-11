<?php

namespace DS\Component\Http;

use DS\Exceptions\RuntimeException;
use DS\Model\NftCollection;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\EachPromise;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use GuzzleRetry\GuzzleRetryMiddleware;
use Orangesoft\Throttler\Collection\Collection;
use Orangesoft\Throttler\Collection\Node;
use Orangesoft\Throttler\Strategy\InMemoryCounter;
use Orangesoft\Throttler\Strategy\WeightedRoundRobinStrategy;
use Orangesoft\Throttler\Throttler;

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
class GeneralHttpClient
{
    /**
     * @var \GuzzleHttp\Client
     */
    protected $httpClient;
    
    /**
     * @var Response
     */
    protected $lastResponse;
    
    /**
     * @var HttpRequestBatch[]
     */
    protected $batches = [];
    
    /**
     * @param Node[] $proxyNodes
     *
     * @return \GuzzleHttp\Client
     */
    public static function makeProxiedHttpClient(array $proxyNodes = []): \GuzzleHttp\Client
    {
        $throttler = new Throttler(
            new Collection($proxyNodes),
            new WeightedRoundRobinStrategy(new InMemoryCounter())
        );
        
        $handlerStack = HandlerStack::create();
        $handlerStack->push(new ProxyMiddleware($throttler));
        
        return self::makeHttpClient(null, $handlerStack);
    }
    
    /**
     * @param array|null        $config
     * @param HandlerStack|null $handlerStack
     *
     * @return \GuzzleHttp\Client
     */
    public static function makeHttpClient(?array $config = null, ?HandlerStack $handlerStack = null): \GuzzleHttp\Client
    {
        if (!$config)
        {
            
            $jar = new \GuzzleHttp\Cookie\CookieJar();
            
            if ($handlerStack === null)
            {
                $handlerStack = HandlerStack::create();
                $handlerStack->push(
                    GuzzleRetryMiddleware::factory(
                        [
                            'retry_enabled' => true,
                            'max_retry_attempts' => 2,
                            'retry_on_status' => [503, 429, 502, 524, 520],
                        ]
                    )
                );
            }
            
            $headers = [
                'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/74.0.3729.169 Safari/537.36',
                'Connection' => 'keep-alive',
                'Accept' => 'text/plain, application/json, text/html, */*',
                'X-Requested-With' => 'XMLHttpRequest',
                'Accept-Encoding' => 'gzip, deflate, br',
                'Accept-Language' => 'en-US,en;q=0.8',
            ];
            
            $config = [
                'handler' => $handlerStack,
                RequestOptions::HEADERS => $headers,
                RequestOptions::COOKIES => $jar,
                RequestOptions::DECODE_CONTENT => 'gzip',
                RequestOptions::CONNECT_TIMEOUT => 2500,
                RequestOptions::TIMEOUT => 500,
                RequestOptions::READ_TIMEOUT => 500,
                RequestOptions::VERIFY => false,
                RequestOptions::ALLOW_REDIRECTS => [
                    'max' => 10,
                    'strict' => true,
                    'referer' => true,
                    'protocols' => ['https', 'http'],
                    'track_redirects' => false,
                ],
            
            ];
        }
        
        return new \GuzzleHttp\Client($config);
    }
    
    /**
     * OpenseaApi constructor.
     *
     * @param \GuzzleHttp\Client $client
     */
    public function __construct(\GuzzleHttp\Client $client)
    {
        $this->httpClient = $client;
    }
    
    /**
     * @param string $url
     *
     * @return Response|null
     * @throws RuntimeException
     */
    public function getRequest(string $url): ?Response
    {
        
        $response = $this->httpClient->get($url);
        
        if ($response instanceof Response)
        {
            $this->lastResponse = $response;
            
            if ($response->getStatusCode() === 404)
            {
                return null;
            }
            
            if ($response->getStatusCode() !== 200)
            {
                return null;
            }
            
            return $this->lastResponse;
        }
        
        throw new RuntimeException("Error fetching request. Guzzle client response invalid.");
    }
    
    /**
     * @param HttpRequestBatch $batch
     *
     * @return GeneralHttpClient
     */
    public function addAsyncBatch(HttpRequestBatch $batch): self
    {
        $this->batches[] = $batch;
        
        return $this;
    }
    
    /**
     * @param bool $force
     * @param int  $threshold
     */
    public function fetchBatchesAsync($force = false, $threshold = 500): void
    {
        if (!$force && count($this->batches) <= $threshold)
        {
            return;
        }
        
        $promises = (function () {
            foreach ($this->batches as $batch)
            {
                yield $this->httpClient->getAsync(trim($batch->url));
            }
        })();
        
        $eachPromise = new EachPromise(
            $promises, [
                'concurrency' => 25,
                'fulfilled' => function (Response $response, $key) use ($threshold) {
                    if ($response->getStatusCode() === 200)
                    {
                        if (isset($this->batches[$key]))
                        {
                            $batch = $this->batches[$key];
                            unset($this->batches[$key]);
                            
                            if ($batch instanceof HttpRequestBatch)
                            {
                                call_user_func($batch->callback, (string) $response->getBody(), count($this->batches), $threshold, $batch->url);
                            }
                            
                        }
                    }
                },
                'rejected' => function ($reason, $key) {
                    if (isset($this->batches[$key]))
                    {
                        $batch = $this->batches[$key];
                        if ($batch instanceof HttpRequestBatch)
                        {
                            call_user_func($batch->rejectedCallback, $reason, method_exists($reason, 'getCode') ? (int) $reason->getCode() : -1, method_exists($reason, 'getMessage') ? (string) $reason->getMessage() : '');
                        }
                        
                        if ($reason instanceof ConnectException)
                        {
                            echo "Connection error ({$reason->getCode()}): {$reason->getRequest()->getUri()} - {$reason->getMessage()}\n";
                        }
                        elseif ($reason instanceof RequestException)
                        {
                            echo "Request error ({$reason->getCode()}): {$reason->getRequest()->getUri()} - {$reason->getMessage()}\n";
                        }
                        else
                        {
                            var_dump($reason);
                        }
                    }
                },
            ]
        );
        
        $eachPromise->promise()->wait();
        
        // Reset batches
        $this->batches = [];
    }
    
    /**
     * @param string $url
     *
     * @return string
     * @throws RuntimeException
     */
    public function getRequestAsString(string $url): string
    {
        $response = $this->getRequest($url);
        
        return (string) $response->getBody();
    }
    
    /**
     * @param string $url
     *
     * @return mixed
     * @throws RuntimeException
     */
    public function getRequestDecoded(string $url)
    {
        return json_decode($this->getRequestAsString($url), true);
    }
}
