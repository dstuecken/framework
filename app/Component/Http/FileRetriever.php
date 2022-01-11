<?php
namespace DS\Component\Http;

use DS\Component\DiInjection;
use Guzzle\Http\Client;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Http\Message\Response;
use Phalcon\Di;
use Phalcon\Session\Adapter as SessionAdapter;

/**
 * DS-Framework
 *
 * @author Dennis Stücken
 * @license proprietary

 * @copyright Dennis Stücken
 * @link https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component\Http
 */
class FileRetriever extends DiInjection
{

    /**
     * @var Client
     */
    protected $client;

    /**
     * @var Response
     */
    protected $lastResponse;

    /**
     * @var FileRetriever
     */
    protected static $instance;

    /**
     * @return Response
     */
    public function lastResponse()
    {
        return $this->lastResponse;
    }

    /**
     * Returns body of the url. Returns a string error message if there is a 404.
     *
     * @param $fileUrl
     *
     * @return string
     */
    public function get($fileUrl)
    {
        try
        {
            $this->lastResponse = $this->client->get(
                $fileUrl,
                [
                    'Accept-Language' => 'en-US',
                ]
            )->send();

            return $this->lastResponse->getBody(true);
        }
        catch (RequestException $e)
        {
            // 404 not found
        }
        catch (\Exception $e)
        {
        }

        return '';
    }

    /**
     * Instance factory
     *
     * @param Di|null $dependencyInjector
     *
     * @return static
     */
    public static function instance(Di $dependencyInjector = null)
    {
        if (!static::$instance)
        {
            static::$instance = new static($dependencyInjector);
        }

        return static::$instance;
    }

    /**
     * DiInjection constructor.
     *
     * @param Di|\Phalcon\DiInterface $dependencyInjector
     */
    public function __construct(Di $dependencyInjector = null)
    {
        parent::__construct($dependencyInjector);

        $this->client = new Client();
        $this->client->setSslVerification(false, false);
    }

}
