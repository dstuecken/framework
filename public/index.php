<?php
/**
 * DS-Framework
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Controller
 */

use DS\Application;

// Include composer's autoloader
include_once('../vendor/autoload.php');

// Do App Initialization
$di = include('../app/bootstrap/Init.php');

try
{
    if (strpos($_SERVER['HTTP_HOST'], "http://localhost:3000") === 0)
    {
        header("Access-Control-Allow-Origin: *");
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: GET,HEAD,OPTIONS,POST,PUT');
        header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers');
    }
    
    // Handle the request
    $response = Application::initialize($di)->handle(
        \DS\Component\ServiceManager::instance($di)->getRequest()->getURI()
    );
}
catch (Exception $e)
{
    // also store error into file
    @file_put_contents('../system/errors', @file_get_contents('../system/errors') . "\n" . $e->getMessage() . " " . $e->getTraceAsString());
    
    if (!isset($di))
    {
        $di = new \Phalcon\Di\FactoryDefault();
    }
    
    if (!isset($response))
    {
        $response = new \Phalcon\Http\Response();
    }
    
    if (function_exists('xdebug_enable'))
    {
        $response->setContent('<h1>Error:</h1><p>' . $e->getMessage() . '</p><table>' . $e->xdebug_message . '</table>');
    }
    else
    {
        $response->setContent('There was a problem: ' . $e->getMessage());
        
        if (ENV === 'development')
        {
            \Symfony\Component\VarDumper\VarDumper::dump($e->getTrace());
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
