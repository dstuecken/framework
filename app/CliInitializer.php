<?php

namespace DS;

use DS\CliApplication as Application;
use Phalcon\Config;
use Phalcon\DI\FactoryDefault;
use Phalcon\Di\FactoryDefault\Cli;
use Phalcon\Http\Response;
use Phalcon\Loader;

final class CliInitializer
{
    /**
     * @var ?FactoryDefault
     */
    private static $di;
    
    /**
     * Handle request
     *
     * @return mixed
     */
    public static function handleRequest()
    {
        try
        {
            if (null === self::$di)
            {
                throw new \Exception("Error booting from cli, DI not initialized.");
            }
            
            $console = Application::initialize(self::$di);
            $console->setArgs($GLOBALS['argv'], $GLOBALS['argc']);
            
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
    }
    
    /**
     * @param Config $config
     *
     * @return FactoryDefault|null
     */
    public static function boot(Config $config): ?FactoryDefault
    {
        error_reporting(E_ALL);
        
        try
        {
            $di = Initializer::boot($config);
            
            return $di;
        }
        catch (\Exception $e)
        {
            //echo $e->getMessage();
            var_dump($e->getTraceAsString());
            fwrite(STDERR, $e->getMessage() . PHP_EOL);
            exit(1);
        }
    }
}
