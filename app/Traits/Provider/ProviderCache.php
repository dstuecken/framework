<?php

namespace DS\Traits\Provider;

use DS\Component\ProviderLogic\Protocols\ProviderProtocol;
use DS\Component\ProviderLogic\ProviderFactory;
use DS\Model\Client;
use DS\Model\Provider;

/**
 * DS-Framework
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Interfaces
 *
 */
trait ProviderCache
{
    /**
     * @var [ProviderProtocol]
     */
    private $providerApis = [];
    
    /**
     * @param Client $client
     *
     * @return ProviderProtocol
     * @throws \Exception
     */
    public function getProviderByClient(Client $client)
    {
        if (!isset($this->providerApis[$client->getProviderId()]))
        {
            $provider = Provider::findFirstById($client->getProviderId());
            
            if (!$provider)
            {
                throw new \Exception("Provider with id '{$client->getProviderId()}' does not exist.");
            }
            
            $this->providerApis[$client->getProviderId()] = ProviderFactory::providerById($provider->getIdentifier(), $client);
        }
        
        return $this->providerApis[$client->getProviderId()];
    }
}
