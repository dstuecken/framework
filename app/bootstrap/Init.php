<?php
/**
 * DS-Framework
 *
 * App Initialization and DI creation
 *
 * @package DS
 * @version $Version$
 */

use Phalcon\Config;
use Phalcon\DI\FactoryDefault;
use Phalcon\Loader;

try
{
    // APP Version
    define('VERSION', '1.0.2b');

    // Directories
    define('APP_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
    define('ROOT_PATH', dirname(APP_PATH) . DIRECTORY_SEPARATOR);

    // Setting default timezone to PST
    date_default_timezone_set('America/Los_Angeles');

    if (!class_exists('Phalcon\Config'))
    {
        throw new ErrorException('Phalcon is not installed!');
    }

    // Initialize configuration
    $config = new Config(
        include APP_PATH . 'config/Config.php'
    );

    // Environment
    define('ENV', $config['mode']);

    /**
     * Initialize DI Container
     *
     * @global $di
     */
    if (PHP_SAPI === 'cli')
    {
        $di = new \Phalcon\Di\FactoryDefault\Cli();
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
    $di[DS\Constants\Services::CONFIG] = $config;

    // Setting AWS environmental variables to prevent error message:
    // Error retrieving credentials from the instance profile metadata server.
    //putenv(\Aws\Credentials\CredentialProvider::ENV_KEY . '=' . $config->get('files')->aws->credentials->key);
    //putenv(\Aws\Credentials\CredentialProvider::ENV_SECRET . '=' . $config->get('files')->aws->credentials->secret);

    include_once 'Functions.php';

    return $di;
}
catch (Exception $e)
{
    die('Error: ' . $e->getMessage() . ', ' . $e->getTraceAsString());
}
