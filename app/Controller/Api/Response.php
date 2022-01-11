<?php
namespace DS\Controller\Api;

use DS\Component\DiInjection;
use DS\Controller\Api\Meta\Error;
use Phalcon\Di;
use Phalcon\Http\Response as HttpResponse;

/**
 *
 * DS-Framework
 *
 * @author Dennis Stücken
 * @license proprietary

 * @copyright Dennis Stücken
 * @link https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Controller
 */
abstract class Response extends DiInjection
{
    /**
     * @return mixed
     */
    abstract protected function setResponseType();

    /**
     * Wrap api response with an envelope
     *
     * @var bool
     */
    protected $envelope = true;

    /**
     * The http response class itself
     *
     * @var HttpResponse
     */
    protected $response;

    /**
     * @var Error
     */
    protected $error;

    /**
     * @return mixed|HttpResponse
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return Error
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return HttpResponse
     */
    public function send()
    {
        $this->setResponseType();
        $this->response->send();

        die;
    }

    /**
     * Response constructor.
     *
     * @param HttpResponse $response
     */
    public function __construct(Di $di)
    {
        parent::__construct($di);

        $this->response = $di->get('response');
    }
}
