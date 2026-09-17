<?php

namespace DS;

use DS\Component\ServiceManager;
use DS\Constants\Services;
use DS\Controller\ApiController;
use Phalcon\Config;
use Phalcon\DI\FactoryDefault;
use Phalcon\Di\FactoryDefault\Cli;
use Phalcon\Events\Manager;
use Phalcon\Http\Response;
use Phalcon\Loader;

final class Initializer
{
    /**
     * @var FactoryDefault
     */
    private static $di;

    /**
     * @var ?Manager
     */
    private static $eventsManager;

    /**
     * Write exception details to error log file with proper error handling.
     *
     * @param string $baseDir Base directory path
     * @param \Exception $exception The exception to log
     * @return void
     */
    private static function writeErrorToFile(string $baseDir, \Exception $exception): void
    {
        $errorFile = $baseDir . '/system/errors';
        $errorMessage = sprintf(
            "\n[%s] %s %s",
            date('Y-m-d H:i:s'),
            $exception->getMessage(),
            $exception->getTraceAsString()
        );

        try
        {
            $existingContent = '';
            if (is_file($errorFile) && is_readable($errorFile))
            {
                $existingContent = file_get_contents($errorFile);
                if ($existingContent === false)
                {
                    $existingContent = '';
                }
            }

            $errorDir = dirname($errorFile);
            if (!is_dir($errorDir))
            {
                mkdir($errorDir, 0755, true);
            }

            $result = file_put_contents($errorFile, $existingContent . $errorMessage);
            if ($result === false)
            {
                error_log('DS-Framework: Failed to write to error file: ' . $errorFile);
            }
        }
        catch (\Throwable $fileError)
        {
            // Last resort: use PHP's error_log as fallback
            error_log('DS-Framework: Error writing to file (' . $fileError->getMessage() . '): ' . $errorMessage);
        }
    }

    /**
     * Passes on given events manager to Application instance
     *
     * @param Manager $eventsManager
     *
     * @return void
     */
    public static function setEventsManager(Manager $eventsManager)
    {
        self::$eventsManager = $eventsManager;
    }

    /**
     * Handle request
     *
     * @return mixed
     */
    public static function handleRequest()
    {
        // Handle the request
        return Application::initialize(self::$di, self::$eventsManager)
            ->handle(
                ServiceManager::instance(self::$di)->getRequest()->getURI()
            );
    }

    /**
     * Boot up the framework
     *
     * @param Config $config
     *
     * @return FactoryDefault|null
     */
    public static function boot(Config $config): ?FactoryDefault
    {
        $pwd = dirname(__DIR__) . DIRECTORY_SEPARATOR;

        // Do App Initialization
        try
        {
            include_once $pwd . 'app/bootstrap/Functions.php';

            // APP Version
            define('DSFW_VERSION', file_get_contents($pwd . 'VERSION'));
            if (!defined('APP_VERSION'))
            {
                define('APP_VERSION', DSFW_VERSION);
            }

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
            $di['loader']->registerNamespaces((array)$config->get('dirs'))->register();

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

            return $di;
        }
        catch (\Exception $e)
        {
            // Store error to file with proper error handling
            self::writeErrorToFile($pwd, $e);

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

        return null;
    }
}
