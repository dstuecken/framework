<?php

namespace DS\Controller;

use DS\Component\ServiceManager;
use DS\Controller\Api\ActionHandler;
use DS\Controller\Api\Meta\Error;
use DS\Controller\Api\Meta\RecordInterface;
use DS\Controller\Api\Response;
use DS\Controller\Api\Response\Json;
use DS\Controller\Api\Response\Text;
use DS\Exceptions\ApiException;
use DS\Exceptions\ApiNeedsLoginException;
use DS\Exceptions\ApiRouteNotFoundException;
use DS\Model\DataSource\ErrorCodes;
use Phalcon\Di;
use Phalcon\Http\Request;
use Phalcon\Logger;

/**
 * DS
 *
 * @copyright 2017 | Dennis Stücken
 *
 * @version   $Version$
 * @package   DS\Controller
 *
 * @property \Phalcon\Http\Request request
 * @property \Phalcon\Http\Response response
 *
 * @method Di\FactoryDefault getDi()
 */
class ApiController
    extends BaseController
{
    /**
     * @var string
     */
    protected static $controllerNamespace = __NAMESPACE__;

    /**
     * @var string
     */
    protected $actionHandlerClass = \DS\Controller\Api\ActionHandler::class;

    /**
     * @param string $controllerNamespace
     *
     * @return $this
     */
    public static function setControllerNamespace($controllerNamespace)
    {
        self::$controllerNamespace = $controllerNamespace;
    }

    /**
     * Initialize controller and define index view
     */
    public function initialize()
    {
        if (!$this->request)
        {
            $this->request = $this->getDI()->get('request');
        }

        parent::initialize();
    }

    /**
     * Log exceptions thrown within the api method
     *
     * @param \Exception $e
     * @param string $method
     * @param string $action
     * @param string $respüonseType
     */
    protected function logException($e, $method = '', $action = '', $responseType = '')
    {
        // Log exception to sentry
        $client = ServiceManager::instance($this->getDi())->getRavenClient();

        try
        {
            if ($client)
            {
                $client->tags_context(['errorType' => get_class($e)]);
                $client->captureException(
                    $e,
                    null,
                    null,
                    [
                        'method' => $method,
                        'responseType' => $responseType,
                        'action' => $action,
                    ]
                );
            }

            // Log exception to system log
            application()->log(
                (method_exists($e, 'getErrorCode') ? $e->getErrorCode() . ':' : '') . $e->getMessage() . ' - ' .
                (method_exists($e, 'getMore') ? var_export($e->getMore(), true) : '') . ' - Body: ' . $this->request->getRawBody() . ' | ' . str_replace(
                    "\n",
                    "",
                    $e->getTraceAsString()
                ),
                Logger::ERROR
            );
        }
        catch (\Exception $e)
        {
            file_put_contents(ROOT_PATH . 'system/log/failure', $e->getMessage() . ' - ' . $e->getTraceAsString());
        }
    }

    /**
     * @param Request $request
     * @return string
     */
    protected function identifyClassName(Request $request): string
    {
        // Define class name, dependent on request type, default is Get
        $className = 'Get';

        if ($this->request->isPost())
        {
            $className = 'Post';
        }
        elseif ($this->request->isPut())
        {
            $className = 'Put';
        }
        elseif ($this->request->isDelete())
        {
            $className = 'Delete';
        }
        elseif ($this->request->isPatch())
        {
            $className = 'Patch';
        }
        elseif ($this->request->isOptions())
        {
            $className = 'Options';
        }

        return $className;
    }

    /**
     * @param string $responseType
     * @return Response
     */
    protected function prepareResponse(string $responseType): Response
    {
        // Set response type, default is Json
        if ($responseType === 'string')
        {
            $response = new Text($this->getDi());
        }
        else
        {
            $response = new Json($this->getDi());
        }

        return $response;
    }

    /**
     * @param string $method
     * @return string
     */
    private function prepareMethod(string $method): string
    {
        // Camel casing a minus-separated request
        if (strpos($method, '-') > 0)
        {
            $method = str_replace(' ', '', ucwords(str_replace('-', ' ', $method)));
        }
        else
        {
            $method = ucfirst($method);
        }

        return $method;
    }

    /**
     * @param string $namespace
     * @param string $action
     * @return string
     */
    private function findClassName(string $namespace, string $action): string
    {
        $className      = $this->identifyClassName($this->request);
        $finalClassName = $namespace . $className;

        // Check if a subclass exist
        if ($action)
        {
            $actionClassName = $namespace . ucfirst($action) . '\\' . $className;
            if (class_exists($actionClassName))
            {
                $finalClassName = $actionClassName;
            }
        }

        return $finalClassName;
    }

    /**
     * @param string $finalClassName
     * @return void
     * @throws ApiRouteNotFoundException
     */
    private function verifyClassName(string $finalClassName): void
    {
        if (!class_exists($finalClassName) || !is_a($finalClassName, $this->actionHandlerClass, true))
        {
            throw new ApiRouteNotFoundException('Route not found.');
        }
    }

    /**
     * @param ActionHandler $ctrlInstance
     * @return void
     * @throws ApiNeedsLoginException
     */
    private function verifySession(ActionHandler $ctrlInstance): void
    {
        $auth = ServiceManager::instance($this->getDI())->getAuth();

        // Check wheather the controller instance needs a valid login or not
        if ($ctrlInstance->needsLogin() && !$auth->loggedIn())
        {
            throw new ApiNeedsLoginException('You need to be logged in to access this resource.');
        }
    }

    /**
     * @param ActionHandler $ctrlInstance
     * @param Response $response
     * @return void
     */
    private function handleCache(ActionHandler $ctrlInstance, Response $response): void
    {
        // E-Tag handling
        $etag = $ctrlInstance->getEtag();

        if ($etag)
        {
            $response->getResponse()->setEtag($etag)->setHeader('Pragma', 'cache');
            $retag = $this->request->getHeader('if-none-match');

            if ($retag && $retag === $etag)
            {
                $response->getResponse()->setHeader('Cache-Control', 'must-revalidate');
                $response->getResponse()->setNotModified();
                $response->getResponse()->send();
                die;
            }
            else
            {
                $response->getResponse()->setCache(60 * 24);
            }
        }
    }

    /**
     * Default index request
     *
     * @return void
     */
    public function routeAction()
    {
        // Disable view processing since the api has it's own responses
        $this->view->disable();

        // Prepare some request variables
        $version = $this->dispatcher->getParam("version");
        $method  = $this->prepareMethod($this->dispatcher->getParam("method") ?? '');
        $action  = $this->dispatcher->getParam("subaction");
        $params  = $this->dispatcher->getParams();

        unset($params['version'], $params['method'], $params['subaction']);

        /**
         * Switch between response types, default is json
         */
        $responseType = $this->request->get('type', 'string', 'json');
        $response     = $this->prepareResponse($responseType);

        // Construct classname of the action handler
        $namespace = self::$controllerNamespace . '\Api\v' . $version . '\\' . $method . '\\';

        try
        {
            $finalClassName = $this->findClassName($namespace, $action);

            // Check if api controller exists
            $this->verifyClassName($finalClassName);

            /**
             * @var $ctrlInstance \DS\Controller\Api\ActionHandler
             */
            $ctrlInstance = new $finalClassName();

            /**
             * @var Di $di
             */
            $di = $this->getDi();
            $ctrlInstance->setDi($di);
            $ctrlInstance->setServiceManager($this->serviceManager);

            $this->verifySession($ctrlInstance);

            /**
             * Set id and action for current action
             */
            $ctrlInstance->setAction($action)->setParams($params);
            if (!empty($params))
            {
                $ctrlInstance->setId($params[count($params) - 1]);
            }

            $this->handleCache($ctrlInstance, $response);

            // Call process method to process the request or initialize the controller
            if (method_exists($ctrlInstance, $action))
            {
                $actionResult = $ctrlInstance->$action();
            }
            else
            {
                $actionResult = $ctrlInstance->process();
            }

            // Attach action result to response
            if ($actionResult instanceof RecordInterface)
            {
                $response->set($actionResult, $actionResult->getHTTPStatusCode() !== 200);
            }
            elseif ($actionResult instanceof Error)
            {
                // Handle possible errors
                $response->setError($actionResult);
            }
            else
            {
                $response->set(null, false);
            }

        }
        catch (\Error $e)
        {
            $response->setError(
                new Error(
                    $e->getMessage() . ' (' . str_replace(ROOT_PATH, '', $e->getFile()) . ':' . $e->getLine() . ')',
                    'There was an internal error.',
                    ErrorCodes::GeneralException
                )
            );
            $this->logException($e, $method, $action, $responseType);
        }
        catch (ApiRouteNotFoundException $e)
        {
            $response->setError(new Error($e->getMessage(), $e->getMessage(), ErrorCodes::InvalidParameter));
        }
        catch (ApiNeedsLoginException $e)
        {
            $response->setError(
                new Error(
                    'Session Error',
                    'It seems like your session timed out. Please sign-in.',
                    ErrorCodes::SessionExpired
                )
            );
        }
        catch (ApiException $e)
        {
            $error = new Error('Api Error', $e->getMessage(), ErrorCodes::ApiError);
            $error
                ->setMore($e->getMore())
                ->setDevMessage($e->getDevMessage())
                ->setErrorCode($e->getErrorCode());

            $response->setError($error);

            $this->logException($e, $method, $action, $responseType);
        }
        finally
        {
            if (!$response->getError() && is_null($response->getResponse()->getContent()))
            {
                $response->setError(new Error('Api Error.', 'There was an internal error contacting the Api.', 'No response set by the api method.'));
            }

            /**
             * Route to specific method
             */
            $response->send();
        }
    }
}
