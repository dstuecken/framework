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
    $config = $application->getConfig();

    // Setup a base URL
    $di->setShared(
        'url',
        function () use ($config) {
            $url = new \Phalcon\Url();
            $url->setBaseUri($config->get('baseurl', '/'));

            return $url;
        }
    );
};
