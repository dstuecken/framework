<?php

use Phalcon\Crypt;

/**
 * https://www.dvlpr.de
 *
 * Crypt Initialization
 *
 * @package DS
 * @version $Version$
 */
return function (\DS\Interfaces\GeneralApplication $application, Phalcon\Di\FactoryDefault $di) {
    /**
     * Setup Slack Errors Notifier
     *
     * @return Crypt
     */
    $di->setShared(
        'slack',
        function () use ($application) {
            return new DS\Component\Slack\Slack($application->getConfig()['slack']);
        }
    );
};
