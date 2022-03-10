<?php
/**
 * DS-Framework
 *
 * URL Service Initialization
 *
 * @package DS
 * @version $Version$
 */

return function (\DS\Interfaces\GeneralApplication $application, Phalcon\Di\FactoryDefault $di) {
    $di->setShared(
        'view',
        function () use ($application, $di) {
            $view = new \Phalcon\Mvc\View();

            $view->setViewsDir($application->getRootDirectory() . 'app/Views/');
            $view->registerEngines(
                [
                    ".volt" =>
                        function (\Phalcon\Mvc\View $view) use ($application, $di) {
                            return new \DS\Component\View\Volt\VoltAdapter($view, $di, $application);
                        },
                ]
            );
            
            if ($eventsManager = \DS\Component\ServiceManager::instance($di)->getEventsManager())
            {
                $view->setEventsManager($eventsManager);
            }
            
            return $view;
        }
    );
};
