<?php

namespace DS;

use DS\Component\Auth;
use DS\Component\ServiceManager;
use DS\Constants\Services;
use DS\Exceptions\UserNoAccessException;
use DS\Interfaces\GeneralApplication;
use DS\Traits\EventsAwareTrait;
use Phalcon\Config;
use Phalcon\Di\FactoryDefault;
use Phalcon\Events\EventsAwareInterface;
use Phalcon\Events\ManagerInterface;
use Phalcon\Exception;
use Phalcon\Logger;
use Phalcon\Mvc\Application as PhalconApplication;

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
class Application
    extends PhalconApplication
    implements GeneralApplication, EventsAwareInterface
{
    use EventsAwareTrait;
    
    /**
     * @var Application
     */
    protected static $instance = null;
    
    /**
     * @var string
     */
    protected static $baseUri = '/';
    
    /**
     * @var
     */
    protected $rootDirectory;
    
    /**
     * @var \Phalcon\Config
     */
    protected $config = null;
    
    /**
     * @var Logger\Adapter
     */
    protected $logger = null;
    
    /**
     * @var ServiceManager
     */
    protected $serviceManager;
    
    /**
     * Root directory with ending /
     *
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
        return ENV;
    }
    
    /**
     * @return \Phalcon\Config
     */
    public function getConfig(): \Phalcon\Config
    {
        return $this->config;
    }
    
    /**
     * @return Auth
     */
    public function getAuth(): Auth
    {
        return $this->di->get(Services::AUTH);
    }
    
    /**
     * @return ServiceManager
     */
    public function getServiceManager(): ServiceManager
    {
        return $this->serviceManager;
    }
    
    /**
     * @param FactoryDefault $di
     * @param Config         $config
     */
    public function __construct(FactoryDefault $di)
    {
        parent::__construct($di);
        
        $this->rootDirectory = dirname(__DIR__) . '/';
        $this->config        = $di[Services::CONFIG];
        
        /**
         * Listen for uncaught exceptions and hidden warnings
         */
        if ($this->getMode() === 'development')
        {
            $di->set(
                Services::DEBUG,
                function () {
                    return (new \Phalcon\Debug())->listen()->listenExceptions()->listenLowSeverity();
                }
            );
            
            $di->get(Services::DEBUG);
        }
    }
    
    /**
     * @param FactoryDefault   $di
     * @param ManagerInterface $manager
     *
     * @return Application
     */
    public static function initialize(FactoryDefault $di, ?ManagerInterface $manager): Application
    {
        // Initialize protected instance
        if (!self::$instance)
        {
            self::$instance = new Application($di);
        }
        
        // Pass on events manager if set
        if (null !== $manager)
        {
            self::$instance->setEventsManager($manager);
        }
        
        // Add application to dependency manager
        $di->set(Services::APPLICATION, self::$instance);
        
        try
        {
            self::$instance
                // Register services
                ->registerServices()
                // Do session management
                ->sessionManagement();
            
            // Pass on event-manager to databases
            if (null !== $manager)
            {
                self::$instance->serviceManager->getReadDatabase()->setEventsManager($manager);
                self::$instance->serviceManager->getWriteDatabase()->setEventsManager($manager);
            }
        }
        catch (UserNoAccessException $e)
        {
            ServiceManager::instance($di)->getFlash()->error($e->getMessage());
            ServiceManager::instance($di)->getAuth()->logout();
            
            ServiceManager::instance($di)->getResponse()->redirect('/login');
        }
        
        return self::$instance;
    }
    
    /**
     * @return Application
     * @throws UserNoAccessException
     */
    public function sessionManagement(): Application
    {
        $this->serviceManager->setEventsManager($this->getEventsManager());
        $this->getEventsManager()->fire('application:beforeSessionManagement', $this);
        
        // Initialize session by accessing auth from DI; This should stay here, otherwize the session will
        // start at the first ->loggedIn() call in the template, which is far too late
        $auth = $this->serviceManager->getAuth();
        
        $this->getEventsManager()->fire('application:afterSessionManagement', $this, ['auth' => $auth, 'serviceManager' => $this->serviceManager]);
        
        return $this;
    }
    
    /**
     * @param     $message
     * @param int $type
     *
     * @return $this
     */
    public function log($message, $type = Logger::INFO): GeneralApplication
    {
        if (!$this->logger)
        {
            $this->logger = $this->serviceManager->getLogger();
        }
        
        $this->logger->log($type, $message);
        
        // Send error message to slack
        if ($type <= Logger::ERROR)
        {
            ob_start();
            debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
            $trace = ob_get_clean();
            
            // $this->serviceManager->getSlack()->sendErrorMessage($message . ' - ' . "\nTrace:\n" . $trace);
            // $this->serviceManager->getMixpanel()->track('Error', ['message' => $message, 'trace' => $trace]);
        }
        
        return $this;
    }
    
    /**
     * @return Application
     * @throws Exception
     */
    public static function instance(): Application
    {
        if (!self::$instance)
        {
            throw new Exception('Application not initialized, yet.');
        }
        
        return self::$instance;
    }
    
    /**
     * Prevent cloning
     */
    final private function __clone()
    {
    }
    
    /**
     * Register DI Services
     *
     * @return $this
     */
    private function registerServices(): Application
    {
        $this->getEventsManager()->fire('application:beforeRegisterServices', $this);
        
        // Get base Uri
        self::$baseUri = $this->config['baseurl'];
        
        /**
         * Initialize all required services
         *
         * @since Phalcon 3.0
         * @note  needed to change from ServiceManager::initialize() to a non static context because phalcon 3.0
         *        changed the way service closures are bound to an object
         * @see   https://github.com/phalcon/cphalcon/issues/11029#issuecomment-200612702
         */
        $this->serviceManager = ServiceManager::instance($this->getDI());
        $this->serviceManager->setEventsManager($this->getEventsManager());
        $this->serviceManager->initialize($this);
        
        if ($this->getMode() === 'development')
        {
            if ($this->getDI()->has('whoops'))
            {
                /**
                 * @var $whoops \Whoops\Run
                 */
                $whoops = $this->getDI()->getShared('whoops');
                $logger = $this->getDI()->get(\DS\Constants\Services::ERRORLOGGER);
                $whoops
                    ->pushHandler(new \Whoops\Handler\JsonResponseHandler())
                    ->pushHandler(
                        function (\Exception $exception, $inspector, $run) use ($logger) {
                            $logger->critical($exception->getMessage());
                            $logger->critical(json_encode($exception->getTrace()));
                        }
                    );
            }
        }
        
        $this->getEventsManager()->fire('application:afterRegisterServices', $this);
        
        return $this;
    }
    
}
