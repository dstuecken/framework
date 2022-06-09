<?php

/**
 * DS-Framework
 *
 * Flysystem Initialization
 *
 * @package DS
 * @version $Version$
 */
return function (\DS\Interfaces\GeneralApplication $application, Phalcon\Di\FactoryDefault $di) {
    /**
     * Setup Flysystem Adapter
     *
     * @return
     */
    $di->setShared(
        'files',
        function () use ($application, $di) {
            $config = $application->getConfig()->toArray();

            if ($config['files']['service'] === 'aws')
            {
                return \DS\Component\Filesystem\Flysystem\AwsS3FlysystemAdapter::instance($config['files']['aws']);
            }
            
            return \DS\Component\Filesystem\Flysystem\LocalFlysystemAdapter::instance($config['files']['local']);
        }
    );

};
