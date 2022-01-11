<?php
/**
 * DS-Framework
 *
 * Global response service
 *
 * @package DS
 * @version $Version$
 */
return function (\DS\Interfaces\GeneralApplication $application, Phalcon\Di\FactoryDefault $di)
{
    $di->setShared(
        'response',
        function () use ($di)
        {
            return new \Phalcon\Http\Response();
        }
    );
};