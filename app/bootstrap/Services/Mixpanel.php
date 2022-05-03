<?php
/**
 * https://www.dvlpr.de
 *
 * Global response service
 *
 * @package DS
 * @version $Version$
 */
return function (\DS\Interfaces\GeneralApplication $application, Phalcon\Di\FactoryDefault $di) {
    $di->set(
        'mixpanel',
        function () use ($application) {
            $config = $application->getConfig();
            if (isset($config->analytics->mixpanel))
            {
                $options = [];
                if ($application->getMode() == 'development')
                {
                    $options = [
                        "consumers" => [
                            "logger" => "DS\\Component\\Analytics\\MixpanelLoggingConsumer",
                        ],
                        "consumer" => "logger",
                    ];
                }
                
                $mixpanel = new \DS\Component\Analytics\Mixpanel($config['analytics']['mixpanel'], $options);
                
                return $mixpanel;
            }
            else
            {
                throw new \Phalcon\Di\Exception('Could not register Mixpanel client: No key available in Config.');
            }
        }
    );
};
