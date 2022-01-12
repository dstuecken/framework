<?php

namespace DS;

use DS\Component\ServiceManager;
use DS\Constants\Services;
use DS\Controller\ApiController;
use Phalcon\Config;
use Phalcon\DI\FactoryDefault;
use Phalcon\Di\FactoryDefault\Cli;
use Phalcon\Http\Response;
use Phalcon\Loader;

final class Initializer
{
    /**
     * @var FactoryDefault
     */
    private static $di;
    
    /**
     * Handle request
     *
     * @return mixed
     */
    public static function handleRequest()
    {
        // Handle the request
        return Application::initialize(self::$di)->handle(
            ServiceManager::instance(self::$di)->getRequest()->getURI()
        );
    }
    
    /**
     * Boot up the framework
     *
     * @param Config $config
     */
    public static function boot(Config $config)
    {
        $pwd = dirname(__DIR__) . DIRECTORY_SEPARATOR;
        
        // Do App Initialization
        try
        {
            include_once $pwd . 'app/bootstrap/Functions.php';
            
            // APP Version
            define('DSFW_VERSION', '1.0.2b');
            
            // Directories
            define('APP_PATH', __DIR__ . DIRECTORY_SEPARATOR);
            if (!defined('ROOT_PATH'))
            {
                define('ROOT_PATH', dirname(APP_PATH) . DIRECTORY_SEPARATOR);
            }
            
            // Setting default timezone to PST
            date_default_timezone_set('America/Los_Angeles');
            
            if (!class_exists('Phalcon\Config'))
            {
                throw new \ErrorException('Phalcon is not installed!');
            }
            
            // Environment
            define('ENV', $config['mode']);
            
            /**
             * Initialize DI Container
             *
             * @global $di
             */
            if (PHP_SAPI === 'cli')
            {
                $di = new Cli();
            }
            else
            {
                $di = new FactoryDefault();
                
                // Initialize BASE url
                $config['baseurl'] = str_replace(['public/index.php', 'index.php'], '', $di['request']->getServer('SCRIPT_NAME'));
            }
            
            // Register an autoloader
            $di['loader'] = new Loader();
            $di['loader']->registerNamespaces((array) $config->get('dirs'))->register();
            
            // Attach config as a service
            $di[Services::CONFIG] = $config;
            
            // Define API namespace if available in config
            if (isset($config['namespaces']['api']))
            {
                ApiController::setControllerNamespace($config['namespaces']['api']);
            }
        
            // Setting AWS environmental variables to prevent error message:
            // Error retrieving credentials from the instance profile metadata server.
            //putenv(\Aws\Credentials\CredentialProvider::ENV_KEY . '=' . $config->get('files')->aws->credentials->key);
            //putenv(\Aws\Credentials\CredentialProvider::ENV_SECRET . '=' . $config->get('files')->aws->credentials->secret);
            
            /*
            if (strpos($_SERVER['HTTP_HOST'], "http://localhost:3000") === 0)
            {
                header("Access-Control-Allow-Origin: *");
                header('Access-Control-Allow-Credentials: true');
                header('Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT');
                header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers');
            }*/
            
            self::$di = $di;
        }
        catch (\Exception $e)
        {
            // also store error into file
            @file_put_contents($pwd . '/system/errors', @file_get_contents($pwd . '/system/errors') . "\n" . $e->getMessage() . " " . $e->getTraceAsString());
            
            if (!isset($di))
            {
                $di = new FactoryDefault();
            }
            
            if (!isset($response))
            {
                $response = new Response();
            }
            
            if (function_exists('xdebug_enable') && property_exists($e, 'xdebug_message'))
            {
                $response->setContent('<h1>Error:</h1><p>' . $e->getMessage() . '</p><table>' . $e->xdebug_message . '</table>');
            }
            else
            {
                $response->setContent('There was a problem: ' . $e->getMessage());
                
                if (ENV === 'development')
                {
                    \Symfony\Component\VarDumper\VarDumper::dump($e);
                }
                
                application()->log($e->getMessage() . ': ' . $e->getTraceAsString());
                sentryException($e);
            }
            
            $response->setStatusCode(500);
            
            if (function_exists('sentryException'))
            {
                sentryException($e);
            }
        }
        finally
        {
            // Send response
            if (isset($response) && !$response->isSent())
            {
                $response->send();
            }
        }
        
    }
}
