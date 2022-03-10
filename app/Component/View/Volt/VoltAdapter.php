<?php

namespace DS\Component\View\Volt;

use DS\Component\ServiceManager;
use DS\Interfaces\GeneralApplication;
use Phalcon\Di\FactoryDefault;
use Phalcon\Mvc\View\Engine\Volt;
use Phalcon\Mvc\ViewBaseInterface;

/**
 * The 'volt' adapter for View Teamplate/Engine.
 */
class VoltAdapter extends Volt
{
    /**
     * @var array
     */
    private $functions = [
        # misc
        'di',
        'auth',
        'serviceManager',
        
        # php
        'json_decode',
        'strtotime',
        'strip_tags',
        'html_entity_decode',
        'explode',
        'implode',
        'number_format',
        'stripos',
    ];
    
    /**
     * Constructor.
     *
     * @param mixed|\Phalcon\Mvc\ViewBaseInterface $view
     * @param FactoryDefault                       $di
     */
    public function __construct(ViewBaseInterface $view, FactoryDefault $di, GeneralApplication $application)
    {
        parent::__construct($view, $di);
        
        $isDev = $application->getMode() === 'development';
        
        $this->setOptions(
            [
                'separator' => '_',
                "path" => $application->getRootDirectory() . "system/cache/volt/",
                'stat' => true, // setting this to false produces an error "The argument is not initialized or iterable()" for some templates
                'always' => $isDev,
                'prefix' => 'v' . APP_VERSION, // todo: add build name here to ensure it always compiles new on a new production build
            ]
        );
        
        $config = $application->getConfig();
        $view->setVar('url', $config['url']);
        
        $view->setVar('cdn', ServiceManager::instance($di)->getFiles());
        $view->setVar('cdnPath', ServiceManager::instance($di)->getFiles()->getWebPath());
        
        /**
         * Add some functions to the volt compiler
         *
         * @var $compiler Volt\Compiler
         */
        $compiler = $this->getCompiler();
        
        foreach ($this->functions as $func)
        {
            $compiler->addFunction($func, $func);
        }
    }
}
