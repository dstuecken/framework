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
     */
    public function notFoundAction()
    {
        $this->response->setStatusCode(404, 'Not Found');
        ServiceManager::instance($this->getDI())->getMixpanel()->track('Error.404');
        
        $this->callCustomErrorController('notFoundAction');
    }
    
    /**
     * Show 500 error message
     *
     * @param \Exception $exception
     */
    public function errorAction(\Exception $exception)
    {
        $this->response->setStatusCode(500, 'Error');
        $this->view->setVar('error', $exception->getMessage());
        
        ServiceManager::instance($this->getDI())->getMixpanel()->track('Error.500');
        
        sentryException($exception);
        
        $this->callCustomErrorController('errorAction', $exception);
        
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
