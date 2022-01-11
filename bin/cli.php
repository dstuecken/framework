<?php
/**
 * https://www.dvlpr.de CLI Application
 *
 * @copyright 2016 | DS
 *
 * @version   $Version$
 * @package   DS\Controller
 */

use DS\CliApplication as Application;

error_reporting(E_ALL);

// Include composer's autoloader
include_once(__DIR__ . '/../vendor/autoload.php');

// Do App Initialization
$di = include_once(__DIR__ . '/../app/bootstrap/Init.php');

try
{
    $console = Application::initialize($di);
    $console->setArgs($argv, $argc);
    
    /**
     * Run cli application
     */
    $console->run();
    exit();
}
catch (\Exception $e)
{
    //echo $e->getMessage();
    var_dump($e->getTraceAsString());
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    exit(1);
}
