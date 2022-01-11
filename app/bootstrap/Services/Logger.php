<?php

/**
 * DS-Framework
 *
 * Logger Initialization
 *
 * @package DS
 * @version $Version$
 */

return function (\DS\Interfaces\GeneralApplication $application, Phalcon\Di\FactoryDefault $di) {
    $di->set(
        \DS\Constants\Services::ERRORLOGGER,
        function () {
            if (!file_exists(ROOT_PATH . 'system/log/error'))
            {
                touch(ROOT_PATH . 'system/log/error');
            }
            
            return new Phalcon\Logger(
                "error", [
                new \Phalcon\Logger\Adapter\Stream(ROOT_PATH . 'system/log/error'),
            ]
            );
        }
    );
    
    $di->set(
        \DS\Constants\Services::LOGGER,
        function () {
            if (!file_exists(ROOT_PATH . 'system/log/application'))
            {
                touch(ROOT_PATH . 'system/log/application');
            }
            
            return new Phalcon\Logger(
                "application", [
                new \Phalcon\Logger\Adapter\Stream(ROOT_PATH . 'system/log/application'),
            ]
            );
        }
    );
    
    $di->set(
        \DS\Constants\Services::CLILOGGER,
        function () {
            if (!file_exists(ROOT_PATH . 'system/log/cli'))
            {
                touch(ROOT_PATH . 'system/log/cli');
            }
            
            return new Phalcon\Logger(
                "application", [
                new \Phalcon\Logger\Adapter\Stream(ROOT_PATH . 'system/log/cli'),
            ]
            );
        }
    );
};
