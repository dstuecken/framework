<?php

namespace DS\Controller\Api;

use DS\Component\ServiceManager;
use DS\Traits\Controller\NeedsLoginTrait;
use Phalcon\Mvc\Controller as PhalconMvcController;

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
abstract class ActionHandler extends PhalconMvcController
{
    use NeedsLoginTrait;

    /**
     * @var string
     */
    protected $action;

    /**
     * @var array
     */
    protected $params = [];

    /**
     * @var string
     */
    protected $id;

    /**
     * @var ServiceManager
     */
    private $serviceManager;

    /**
     * @var int
     */
    private $userId;

    /**
     * Extract raw body from request
     *
     * @return mixed
     */
    protected function extractBody($leaveAsJson = false)
    {
        /**
         * If raw body does not work in php 7.0, use php://input
         *
         * @see  http://php.net/manual/en/ini.core.php#ini.always-populate-raw-post-data
         */
        return $leaveAsJson ? $this->request->getRawBody() : $this->request->getJsonRawBody(true);
    }

    /**
     * @return ServiceManager
     */
    protected function getServiceManager(): ServiceManager
    {
        if (!$this->serviceManager)
        {
            $this->serviceManager = ServiceManager::instance($this->getDI());
        }

        return $this->serviceManager;
    }

    /**
     * @param ServiceManager $serviceManager
     *
     * @return $this
     */
    public function setServiceManager(ServiceManager $serviceManager): ActionHandler
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }

    /**
     * @return int
     */
    protected function getUserId(): int
    {
        if (!$this->userId)
        {
            $this->userId = $this->getServiceManager()->getAuth()->getUserId();
        }

        return $this->userId;
    }

    /**
     * Default etag is null. Null etags are not sent to browser.
     *
     * @return null
     */
    public function getEtag()
    {
        return null;
    }

    /**
     * @return string
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * @param string $action
     *
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function setParams(array $params): self
    {
        $this->params = $params;

        return $this;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @param Response $response
     *
     * @return $this
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;

        return $this;
    }

    /**
     * Process api request
     *
     * @param array $params
     *
     * @return mixed
     */
    public function process()
    {
        return "This REST method is not supported.";
    }
}
