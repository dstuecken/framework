<?php

/**
 * DS-Framework
 *
 * Request Initialization
 *
 * @package DS
 * @version $Version$
 */
return function (\DS\Interfaces\GeneralApplication $application, Phalcon\Di\FactoryDefault $di)
{
    /**
     * Setup Request
     *
     * @return
     */
    $di->setShared(
        'request',
        function () use ($application, $di): \Phalcon\Http\Request
        {
            return new \Phalcon\Http\Request();
        }
    );

};
