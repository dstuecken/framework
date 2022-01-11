<?php

namespace DS\Component\ProviderLogic\Api\SendGrid;

use Phalcon\Http\Client\Provider\Curl;
use Phalcon\Logger;
use SendGrid\Response;

/**
 * Badges
 *
 * @author            Dennis Stücken
 * @license           proprietary
 * @copyright         https://www.dvlpr.de
 * @link              https://www.dvlpr.de
 *
 * @version           $Version$
 * @package           DS\Model
 *
 * @method static findFirstById(int $id)
 */
class SendGrid extends \SendGrid
{
    /**
     * @var Response
     */
    private $lastResponse;
    
    /**
     * @return Response
     */
    public function getLastResponse(): Response
    {
        return $this->lastResponse;
    }
    
    /**
     * @return int
     */
    public function getRemainingRateLimit(): int
    {
        if (!$this->lastResponse)
        {
            return 500;
        }
        
        $headers = $this->lastResponse->headers(true);
        
        if (!isset($headers['x-ratelimit-remaining']))
        {
            return 500;
        }
        
        return (int) $headers['x-ratelimit-remaining'];
    }
    
    /**
     * @param Response $response
     *
     * @return mixed
     */
    private function response(Response $response)
    {
        $this->lastResponse = $response;
        
        return json_decode($response->body(), false);
    }
}
