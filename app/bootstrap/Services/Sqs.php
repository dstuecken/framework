<?php
/**
 * DS-Framework
 *
 * @package DS
 * @version $Version$
 */
return function (\DS\Interfaces\GeneralApplication $application, Phalcon\Di\FactoryDefault $di) {
    $di->setShared(
        'sqsClient',
        function () use ($application) {
            $config = $application->getConfig();
            $creds  = new \Aws\Credentials\Credentials(
                $config->get('files')->aws->credentials->key,
                $config->get('files')->aws->credentials->secret
            );
            
            return new \Aws\Sqs\SqsClient(
                [
                    'credentials' => \Aws\Credentials\CredentialProvider::fromCredentials($creds),
                    'region' => 'us-west-2',
                    'version' => 'latest',
                    'signature_version' => 'v4',
                ]
            );
        }
    );
    
};
