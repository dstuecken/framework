<?php

namespace DS;

use DS\Cli\Interaction\Output;
use DS\Component\ServiceManager;
use DS\Constants\Services;
use DS\Interfaces\GeneralApplication;
use Phalcon\Cli\Console;
use Phalcon\Config;
use Phalcon\Di\DiInterface;
use Phalcon\Events\ManagerInterface;
use Phalcon\Exception;
use Phalcon\Logger;
use Phalcon\Logger\Adapter\Stream as StreamAdapter;

/**
 * DS-Framework Application
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Controller
 */
class CliApplication
    extends Console
    implements GeneralApplication
{
    /**
     * @var int
     */
    protected $argc = 0;
    
    /**
     * @var array
     */
    protected $argv = [];
    
    /**
     * @var string
     */
    protected $task = '';
    
    /**
     * @var string
     */
    protected $action = '';
    
    /**
     * @var array
     */
    protected $params = [];
    
    /**
     * @var array
     */
    protected $flags = [];
    
    /**
     * @var Logger
     */
    protected $logger = null;
    
    /**
     * @var Config
     */
    protected $config = null;
    
    /**
     * @var bool
     */
    protected $debug = true;
    
    /**
     * @var string
     */
    protected $rootDirectory = '';
    
    /**
     * @var CliApplication
     */
    protected static $instance = null;
    
    /**
     * @var ServiceManager
     */
    protected $serviceManager = null;
    
    /**
     * @return string
     */
    public function getRootDirectory(): string
    {
        return $this->rootDirectory;
    }
    
    /**
     * Return current running mode.
     *
     * Can either be production, staging or development
     *
     * @return string
     */
    public function getMode(): string
    {
        return isset($this->config['mode']) ? $this->config['mode'] : 'production';
    }
    
    /**
     * @return \Phalcon\Config
     */
    public function getConfig(): \Phalcon\Config
    {
        return $this->config;
    }
    
    /**
     * @return ServiceManager
     */
    public function getServiceManager(): ServiceManager
    {
        return $this->serviceManager;
    }
    
    /**
     * @param ServiceManager $serviceManager
     *
     * @return $this
     */
    public function setServiceManager($serviceManager): CliApplication
    {
        $this->serviceManager = $serviceManager;
        
        return $this;
    }
    
    /**
     * @param DiInterface           $di
     * @param ManagerInterface|null $manager
     *
     * @return CliApplication
     * @throws Exception
     */
    public static function initialize(DiInterface $di, ?ManagerInterface $manager): CliApplication
    {
        if (!self::$instance)
        {
            self::$instance = new self($di);
        }
        
        // Pass on events manager if set
        if (null !== $manager)
        {
            self::$instance->setEventsManager($manager);
        }
        
        /**
         * Initialize all required services
         *
         * @since Phalcon 3.0
         * @note  needed to change from ServiceManager::initialize() to a non static context because phalcon 3.0
         *        changed the way service closures are bound to an object
         * @see   https://github.com/phalcon/cphalcon/issues/11029#issuecomment-200612702
         */
        $servMan = ServiceManager::instance($di);
        $servMan->setEventsManager($manager);
        $servMan->initialize(self::$instance, ['Router', 'Response']);
        
        self::$instance->setServiceManager($servMan);
        self::$instance->logger = $servMan->getCliLogger();
        self::$instance->logger->addAdapter('stdout', new StreamAdapter('php://stdout'));
        
        /**
         * Active sentry bug tracking
         */
        if (self::$instance->getConfig()['mode'] !== 'development')
        {
            $servMan->getRavenClient();
        }
        
        return self::instance();
    }
    
    /**
     * @return array
     */
    public function getCliArguments(): array
    {
        return $this->argv;
    }
    
    /**
     * @return bool
     */
    public function isDebug()
    {
        return $this->debug;
    }
    
    /**
     * @param bool $debug
     *
     * @return $this
     */
    public function setDebug($debug)
    {
        $this->debug = $debug;
        
        return $this;
    }
    
    /**
     * @return CliApplication
     * @throws Exception
     */
    public static function instance()
    {
        if (!self::$instance)
        {
            throw new Exception('Cli Application is not initialized, yet.');
        }
        
        return self::$instance;
    }
    
    /**
     * @return Logger\Multiple
     */
    public function getLogger()
    {
        return $this->logger;
    }
    
    /**
     * Set Application arguments
     *
     * @param array
     * @param int
     */
    public function setArgs($argv, $argc)
    {
        $this->argv = $argv;
        $this->argc = $argc;
    }
    
    /**
     * Get the task/action to direct to
     *
     * @param array $flags cli arguments to determine tasks/action/param
     *
     * @throws Exception
     */
    protected function determineTask($flags)
    {
        if (is_array($flags))
        {
            // Since first argument is the name so script executing (pop it off the list)
            array_shift($flags);
            
            if (is_array($flags) && !empty($flags))
            {
                foreach ($flags as $flag)
                {
                    if ($this->isParameter($flag))
                    {
                        $param = explode('=', str_replace('--', '', $flag));
                        if (isset($param[1]))
                        {
                            $this->params[$param[0]] = $param[1];
                        }
                    }
                    elseif ($this->isFlag($flag))
                    {
                        $this->flags[] = substr($flag, 1);
                    }
                    elseif (empty($this->task))
                    {
                        $this->task = $flag;
                    }
                    elseif (empty($this->action))
                    {
                        $this->action = $flag;
                    }
                    else
                    {
                        $this->params[] = $flag;
                    }
                }
            }
            else
            {
                throw new Exception('Unable to determine task/action/params');
            }
        }
        else
        {
            throw new \InvalidArgumentException('flags has to be of type array!');
        }
    }
    
    /**
     * Determine if argument is a special flag
     *
     * @param string
     *
     * @return bool
     */
    protected function isFlag($flag)
    {
        return substr(trim($flag), 0, 1) == '-';
    }
    
    /**
     * Determine if argument is a special flag
     *
     * @param string
     *
     * @return bool
     */
    protected function isParameter($flag)
    {
        return substr(trim($flag), 0, 2) == '--' && strpos($flag, '=') > 0;
    }
    
    /**
     * @param     $message
     * @param int $type
     *
     * @return $this
     */
    public function log($message, $type = Logger::INFO): GeneralApplication
    {
        $this->logger->log($type, $message);
        
        return $this;
    }
    
    /**
     * Print usage screen
     */
    private function usage()
    {
        $output = new Output();
        
        $output
            ->writeln("<Error>Invalid Usage:</Error>")
            ->writeln(" <Note>php</Note> cli/cli.php taskName")
            ->writeln('');
        
        if (isset($this->config['namespaces']['task']))
        {
            $commandsPath = ROOT_PATH . 'app';
        }
        else
        {
            $commandsPath = APP_PATH . 'Task/';
        }
        $directory = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($commandsPath, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        
        $foundTasks = [];
        
        foreach ($directory as $object)
        {
            if ($object->getFileName() === 'TaskHelpers')
            {
                continue;
            }
            
            if (strpos(strrev($object->getFileName()), 'php.ksaT') === 0)
            {
                $foundTasks[] = $object;
            }
        }
        
        if (count($foundTasks) > 0)
        {
            $output->writeln("Possible tasknames are:");
            foreach ($foundTasks as $object)
            {
                $task = str_replace(['.php', 'Task'], '', $object->getFileName());
                
                $output->writeln(' <Comment>' . $task . '</Comment>');
            }
        }
        else
        {
            if (isset($this->config['namespaces']['task']))
            {
                $namespace = $this->config['namespaces']['task'];
            }
            else
            {
                $namespace = 'Not Set (check Config.php)';
            }
            $output->writeln(' There was no task found inside of your root directory: <Comment>' . $commandsPath . '</Comment>.');
            $output->writeln(' Current task namespace is: <Comment>' . $namespace . '</Comment>');
        }
    }
    
    /**
     * Run Task
     */
    public function run()
    {
        try
        {
            if ($debugKey = array_search('--debug', $this->argv))
            {
                $this->setDebug(true);
                
                unset($this->argv[$debugKey]);
            }
            
            $exit = 0;
            
            if ($this->argc === 1)
            {
                $this->usage();
                
                return 1;
            }
            
            $this->determineTask($this->argv);
            
            $args = [];
            
            if (!$this->task)
            {
                $this->task = 'Main';
            }
            
            // Setup args (task, action, params) for console
            if (isset($this->config['namespaces']['task']))
            {
                $namespace    = $this->config['namespaces']['task'];
                $args['task'] = $namespace . '\\' . $this->task;
            }
            else
            {
                $args['task'] = "DS\\Task\\" . $this->task;
            }
            
            $args['action'] = !empty($this->action) ? $this->action : 'main';
            $args['params'] = $this->params;
            $args['flags']  = $this->flags;
            
            // Kick off Task
            $this->handle($args);
            
        }
        catch (\Phalcon\Exception $exception)
        {
            $exit = 1;
            //echo $exception->getMessage();
            fwrite(STDERR, $exception->getMessage() . PHP_EOL);
            var_dump($exception->getTraceAsString());
        }
        catch (\Throwable $exception)
        {
            $exit = 1;
            //echo $exception->getMessage();
            fwrite(STDERR, $exception->getMessage() . PHP_EOL);
            var_dump($exception->getTraceAsString());
        }
        
        return $exit;
    }
    
    /**
     * @param DiInterface $di
     */
    public function __construct(DiInterface $di)
    {
        parent::__construct($di);
        
        $this->rootDirectory = dirname(__DIR__) . '/';
        $this->config        = $di[Services::CONFIG];
    }
}
