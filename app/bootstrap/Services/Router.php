<?php

use DS\Interfaces\LoginAwareController;
use Phalcon\Mvc\Dispatcher as PhDispatcher;
use Phalcon\Mvc\Router;

/**
 * DS-Framework
 *
 * Router Initialization
 *
 * @package DS
 * @version $Version$
 */

return function (\DS\Interfaces\GeneralApplication $application, Phalcon\Di\FactoryDefault $di) {
    // Register Router
    $router = new Router();
    $router->setDefaultNamespace('DS\Controller');
    
    $router->setDefaultController('Index');
    $router->setDefaultAction('index');
    $router->removeExtraSlashes(true);
    
    // Api Routes
    $router->add(
        "/api/v{version:[0-9]}/{method:[a-zA-Z0-9\-]+}",
        [
            'controller' => 'Api',
            'action' => 'route',
        ]
    );
    $router->add(
        "/api/v{version:[0-9]}/{method:[a-zA-Z0-9\-]+}/:action",
        [
            'controller' => 'Api',
            'action' => 'route',
            'subaction' => 3,
        ]
    );
    $router->add(
        "/api/v{version:[0-9]}/{method:[a-zA-Z0-9\-]+}/:action/([a-zA-Z0-9\-]+)",
        [
            'controller' => 'Api',
            'action' => 'route',
            'subaction' => 3,
            'id' => 4,
        ]
    );
    
    $router->add(
        '/:controller/([a-zA-Z\-]+)/:params',
        [
            'controller' => 1,
            'action' => 2,
            'params' => 3,
        ]
    )->convert(
        'action',
        function ($action) {
            return Phalcon\Text::camelize($action);
        }
    );
    
    /**
     * Attach manual routes
     * @todo: better way to attach routes to the framework
    $manualRoutes = include __DIR__ . '../../bootstrap/Routes.php';
    foreach ($manualRoutes as $route)
    {
        $router
            ->add(
                $route['url'],
                $route['paths'],
                $route['methods'] ?? ['GET', 'POST']
            )
            ->setName($route['name'] ?? $route['url']);
    }*/
    
    // Register a 404
    // This unfortunately did not work, so i am using the dispatcher workaround below
    /*$router->notFound(
        array(
            "namespace"  => "DS\Controller",
            "controller" => "Error",
            "action"     => "show"
        )
    );*/
    
    $di->setShared('router', $router);
    
    /**
     * Setup dispatcher and register a 404 template
     */
    $di->setShared(
        'dispatcher',
        function () use ($di, $application) {
            $evManager = $di->getShared('eventsManager');
            
            /** @noinspection PhpUnusedParameterInspection */
            /*
            $evManager->attach(
                "dispatch:afterDispatch",
                function ($event, PhDispatcher $dispatcher) use($di)
                {
                }
            );
            */
            
            /** @noinspection PhpUnusedParameterInspection */
            $evManager->attach(
                "dispatch:beforeExecuteRoute",
                function ($event, PhDispatcher $dispatcher) use ($di) {
                    // Enable camelized routes, so like my-route is transformed to MyRoute.
                    $actionName     = \Phalcon\Text::camelize($dispatcher->getActionName());
                    $controllerName = \Phalcon\Text::camelize($dispatcher->getControllerName());
                    $dispatcher->setActionName($actionName);
                    $dispatcher->setControllerName($controllerName);
                    
                    // Check if this page needs a login
                    $ctrl = $dispatcher->getActiveController();
                    
                    if ($controllerName !== 'Login'
                        && is_a($ctrl, LoginAwareController::class))
                    {
                        /**
                         * @var $ctrl DS\Interfaces\LoginAwareController
                         */
                        if ($ctrl->needsLogin() && !\DS\Component\ServiceManager::instance($di)->getAuth()->loggedIn())
                        {
                            // @todo may use http redirect instead?
                            //$response = new \Phalcon\Http\Response();
                            //$response->redirect('login');
                            
                            // .. and if so, redirect to the login page
                            $dispatcher->forward(
                                [
                                    'controller' => 'Login',
                                    'action' => 'index',
                                ]
                            );
                        }
                    }
                    
                    return true;
                }
            );
            
            /** @noinspection PhpUnusedParameterInspection */
            $evManager->attach(
                "dispatch:beforeException",
                function ($event, PhDispatcher $dispatcher, Exception $exception) {
                    \DS\Component\ServiceManager::instance($dispatcher->getDI())->getLogger()->debug($exception->getMessage());
                    
                    switch ($exception->getCode())
                    {
                        case 2: // Controller not found
                        case 5: // Index was not found
                            $dispatcher->forward(
                                [
                                    'controller' => 'Error',
                                    'action' => 'notFound',
                                ]
                            );
                            break;
                        default:
                        case 1:
                            $dispatcher->forward(
                                [
                                    'controller' => 'Error',
                                    'action' => 'error',
                                    'params' => [
                                        $exception,
                                    ],
                                ]
                            );
                            break;
                    }
                    
                    return false;
                }
            );
            
            $dispatcher = new PhDispatcher();
            $dispatcher->setEventsManager($evManager);
            
            return $dispatcher;
        }
    );
};
