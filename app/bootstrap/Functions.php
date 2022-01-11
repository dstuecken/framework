<?php
/**
 * This file is used to define some service functions that will be enabled from within the views and everywhere else
 */

function isProduction(): bool
{
    return ENV === 'production';
}

if (!function_exists('di'))
{
    /**
     * @param null $service
     *
     * @return \Phalcon\DiInterface
     */
    function di($service = null)
    {
        $default = \Phalcon\Di::getDefault();
        
        if ($service !== null)
        {
            return $default->get($service);
        }
        
        return $default;
    }
}

if (!function_exists('application'))
{
    /**
     * @return \DS\Interfaces\GeneralApplication
     */
    function application()
    {
        if (PHP_SAPI === 'cli')
        {
            return \DS\CliApplication::instance();
        }
        
        return \DS\Application::instance();
    }
}

if (!function_exists('auth'))
{
    /**
     * @return \DS\Component\Auth
     */
    function auth()
    {
        return di()->get('auth');
    }
}

if (!function_exists('request'))
{
    /**
     * @return \Phalcon\Http\Request
     */
    function request()
    {
        return di()->get('request');
    }
}

if (!function_exists('serviceManager'))
{
    /**
     * @return \DS\Component\ServiceManager
     */
    function serviceManager()
    {
        return \DS\Component\ServiceManager::instance(di());
    }
}

if (!function_exists('sentryMessage'))
{
    /**
     * @param       $message
     * @param array $params
     * @param array $data
     *
     * @return Raven_Client
     */
    function sentryMessage($message, $params = [], $data = [])
    {
        // Log exception to sentry
        $client = serviceManager()->getRavenClient();
        
        $client->tags_context(['errorType' => 'generalError']);
        $client->captureMessage($message, $params, $data);
        
        return $client;
    }
}

if (!function_exists('sentryException'))
{
    /**
     * @param Exception $e
     * @param array     $data
     *
     * @return Raven_Client
     */
    function sentryException(\Exception $e, $data = [])
    {
        // Log exception to sentry
        if (method_exists(serviceManager(), "getRavenClient"))
        {
            $client = serviceManager()->getRavenClient();
            
            $client->tags_context(['errorType' => 'generalError']);
            $client->captureException($e, $data);
            
            return $client;
        }
    }
}

