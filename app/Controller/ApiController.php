<?php

namespace DS\Controller;

use DS\Component\ServiceManager;
use DS\Controller\Api\Meta\Error;
use DS\Controller\Api\Meta\RecordInterface;
use DS\Controller\Api\Response\Json;
use DS\Controller\Api\Response\Text;
use DS\Exceptions\ApiException;
use DS\Exceptions\InvalidParameterException;
use DS\Model\DataSource\ErrorCodes;
use Phalcon\Di;
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
     * Default index request
     *
     * @return void
     */
    public function routeAction()
    {
        /**
         * @var Di $di
         */
        $di = $this->getDi();

        // Disable view processing since the api has it's own responses
        $this->view->disable();

        // Prepare some request variables
        $version = $this->dispatcher->getParam("version");
        $method  = $this->dispatcher->getParam("method");
        $action  = $this->dispatcher->getParam("subaction");
        $id      = $this->dispatcher->getParam("id");
        $auth    = ServiceManager::instance($this->getDI())->getAuth();;

        /**
         * Switch between response types, default is json
         */
        $responseType = $this->request->get('type', 'string', 'json');

        // Set response type, default is Json
        if ($responseType === 'string')
        {
            $response = new Text($di);
        }
        else
        {
            $response = new Json($di);
        }

        try
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

            // Construct classname of the action handler
            $namespace = self::$controllerNamespace . '\Api\v' . $version . '\\' . $method . '\\';

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

            $finalClassName = $namespace . $className;

            // Check if api controller exists
            if (class_exists($finalClassName) && is_a($finalClassName, $this->actionHandlerClass, true))
            {
                /**
                 * @var $ctrlInstance \DS\Controller\Api\ActionHandler
                 */
                $ctrlInstance = new $finalClassName();
                $ctrlInstance->setDi($di);

                // Check wheather the controller instance needs a valid login or not
                if ($ctrlInstance->needsLogin() && !$auth->loggedIn())
                {
                    $response->setError(
                        new Error(
                            'Session Error',
                            'It seems like your session timed out. Please sign-in.',
                            ErrorCodes::SessionExpired
                        )
                    );
                }
                else
                {
                    /**
                     * Set id and action for current action
                     */
                    $ctrlInstance->setAction($action)->setId($id);

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

                    // Call process method to process the request or initialize the controller
                    $actionResult = $ctrlInstance->process();
                    $ctrlInstance->setServiceManager($this->serviceManager);

                    // Then additionally call action method, if there is one
                    if ($action && method_exists($ctrlInstance, $action))
                    {
                        $actionResult = $ctrlInstance->$action();
                    }

                    // Attach action result to response
                    if ($actionResult instanceof RecordInterface)
                    {
                        $response->set($actionResult, $actionResult->getHTTPStatusCode() !== 200);
                    }
                    else
                    {
                        // Handle possible errors
                        if ($actionResult instanceof Error)
                        {
                            $response->setError($actionResult);
                        }
                        else
                        {
                            $response->set(null, false);
                        }
                    }
                }
            }
            else
            {
                throw new InvalidParameterException('Invalid method: ' . $method);
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
        catch (InvalidParameterException $e)
        {
            $response->setError(new Error($e->getMessage(), $e->getMessage(), ErrorCodes::InvalidParameter));
            //$this->logException($e, $method, $action, $responseType);
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
        catch (\Exception $e)
        {
            $response->setError(new Error('Api Error', $e->getMessage(), ErrorCodes::GeneralException, $e->getTraceAsString()));
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
