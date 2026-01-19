<?php

namespace DS\Controller;

use DS\Component\ServiceManager;
use Phalcon\Mvc\Controller as PhalconMvcController;

/**
 *
 * Spreadshare
 *
 * @author    Rarelytics
 * @license   proprietary
 * @copyright Spreadshare
 * @link      https://www.rarelytics.co
 *
 * @version   $Version$
 * @package   DS\Controller
 */
class ErrorController extends PhalconMvcController
{
    /**
     * Show 404 error message
     *
     * @return \Phalcon\Http\Response
     */
    public function notFoundAction()
    {
        $this->response->setStatusCode(404, 'Not Found');

        return $this->response->setJsonContent(['error' => '404 Not Found']);
    }

    /**
     * Show 500 error message
     *
     * @param \Exception $exception
     * @return \Phalcon\Http\Response
     */
    public function errorAction(\Exception $exception)
    {
        $this->response->setStatusCode(500, 'Error');
        $this->view->setVar('error', $exception->getMessage());

        sentryException($exception);

        $errorResponse = [
            'error' => $exception->getMessage()
        ];

        // Only expose debug details in non-production environments
        if (application()->getMode() !== 'production')
        {
            $errorResponse['file'] = $exception->getFile();
            $errorResponse['line'] = $exception->getLine();
        }

        return $this->response->setJsonContent($errorResponse);
    }
    
    private function callCustomErrorController(string $method, $param = null)
    {
        $router      = $this->di->get('router');
        $customError = $router->getNamespaceName() . '\ErrorController';
        if (class_exists($customError))
        {
            $errorController = new $customError();
            if (method_exists($errorController, $method))
            {
                $errorController->$method($param);
            }
        }
    }
}
