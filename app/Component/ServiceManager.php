<?php

namespace DS\Component;

use Aws\AutoScaling\AutoScalingClient;
use Aws\Sqs\SqsClient;
use DiscordWebhooks\Client;
use DS\Component\Filesystem\Flysystem\DsFlysystemAdapterInterface;
use DS\Component\ProviderLogic\Api\SendGrid\SendGrid;
use DS\Component\Slack\Slack;
use DS\Component\Text\Url;
use DS\Component\Twitter\TwitterApi;
use DS\Component\View\Breadcrumbs\Breadcrumbs;
use DS\Interfaces\GeneralApplication;
use DS\Traits\EventsAwareTrait;
use Phalcon\Cache\Adapter\AdapterInterface;
use Phalcon\Config;
use Phalcon\Di;
use Phalcon\Events\EventsAwareInterface;
use Phalcon\Events\Manager;
use Phalcon\Events\ManagerInterface;
use Phalcon\Flash\FlashInterface;
use Phalcon\Http\Response\Cookies;

/**
 * DS-Framework
 *
 * ServiceManager: Registers all services lying under app/bootstrap/Services.
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component
 *
 * @method Config getConfig()
 * @method \Phalcon\Logger getLogger()
 * @method \Phalcon\Logger getErrorLogger()
 * @method \Phalcon\Logger getCliLogger()
 * @method \DS\Component\Auth getAuth()
 * @method \Phalcon\Queue\Beanstalk\Extended getBeanstalk()
 * @method \DS\Component\Cache\Memcache getMemcache()
 * @method \DS\Component\Cache\Redis getRedis()
 * @method \Phalcon\Http\Request getRequest()
 * @method \Phalcon\Mvc\Model\MetaData\Redis getModelsMetadata()
 * @method \DS\Model\Manager\CachedReusableModelsManager getModelsManager()
 * @method \Phalcon\Crypt getCrypt()
 * @method \Phalcon\Db\Profiler getProfiler()
 * @method \DS\Component\Intl getIntl()
 * @method \DS\Component\Queue\Bernard\BernardQueue getQueue()
 * @method \Phalcon\Http\Response getResponse()
 * @method AdapterInterface getViewCache()
 * @method \Phalcon\Mvc\Router getRouter()
 * @method \Phalcon\Mvc\Model\Transaction\Manager getTransactions()
 * @method Url getUrl()
 * @method \DS\Component\Session\Manager getSession()
 * @method Slack getSlack()
 * @method \Raven_Client getRavenClient();
 * @method Security\ getSecurity();
 * @method Notify getNotify();
 * @method DsFlysystemAdapterInterface getFiles();
 * @method Cookies getCookies();
 * @method TwitterApi getTwitter();
 * @method SendGrid getSendgrid();
 * @method \Phalcon\Security getSecurity();
 * @method \DS\Component\Analytics\Mixpanel getMixpanel();
 * @method \DS\Component\Queue\Bernard\BernardQueue getBernard();
 * @method Breadcrumbs getBreadcrumbs()
 * @method \DS\Component\Payments\Provider\Stripe\Stripe getStripe()
 * @method FlashInterface getFlash()
 * @method AutoScalingClient getAwsAutoscalingClient()
 * @method SqsClient getSqsClient()
 * @method Client getDiscord()
 */
class ServiceManager implements EventsAwareInterface
{
    /**
     * Service Directory
     *
     * @var string
     */
    public static $serviceDir = 'bootstrap/Services';
    
    /**
     * @var ServiceManager
     */
    private static $instance;
    
    /**
     * @var Di
     */
    protected $di;
    
    /**
     * @param ManagerInterface $eventsManager
     *
     * @return void
     */
    public function setEventsManager(ManagerInterface $eventsManager): void
    {
        $this->di->set('eventsManager', $eventsManager);
    }
    
    /**
     * @return ManagerInterface
     */
    public function getEventsManager(): ManagerInterface
    {
        if (!$this->di['eventsManager'])
        {
            $this->setEventsManager(new Manager());
        }
        
        return $this->di['eventsManager'];
    }
    
    /**
     * Sets the dependency injector
     *
     * @param Di $dependencyInjector
     */
    public function setDI(\Phalcon\DiInterface $dependencyInjector)
    {
        $this->di = $dependencyInjector;
        
        return $this;
    }
    
    /**
     * Returns the internal dependency injector
     *
     * @return Di
     */
    public function getDI()
    {
        return $this->di;
    }
    
    /**
     * @return \Phalcon\Db\Adapter\Pdo\Mysql
     */
    public function getWriteDatabase(): \Phalcon\Db\Adapter\Pdo\Mysql
    {
        return $this->di->get('write-database');
    }
    
    /**
     * @return \Phalcon\Db\Adapter\Pdo\Mysql
     */
    public function getReadDatabase(): \Phalcon\Db\Adapter\Pdo\Mysql
    {
        return $this->di->get('read-database');
    }
    
    /**
     * @param Di|null $dependencyInjector
     *
     * @return ServiceManager
     */
    public static function instance(Di $dependencyInjector = null): ServiceManager
    {
        if (!self::$instance)
        {
            self::$instance = new self($dependencyInjector);
        }
        
        return self::$instance;
    }
    
    /**
     * Initialize all relevant DI services
     *
     * @param GeneralApplication $application
     * @param array              $excludeServices
     *
     * @return $this
     */
    public function initialize(GeneralApplication $application, $excludeServices = [])
    {
        $di = $this->getDI();
        
        $directory = __DIR__ . '/../' . self::$serviceDir . '/';
        
        $exclude = count($excludeServices) > 0;
        
        if ($dirhandle = opendir($directory))
        {
            while (($file = readdir($dirhandle)) !== false)
            {
                if ($file === '.' || $file === '..' || $file === 'unused')
                {
                    continue;
                }
                
                if ($exclude && in_array(str_replace('.php', '', $file), $excludeServices))
                {
                    continue;
                }
                
                // Registering server "$file"
                $return = include_once $directory . $file;
                
                //if (is_callable($return))
                {
                    // Register service
                    $return ($application, $di);
                }
                // else: Service is registered elsewhere
            }
        }
        
        $this->getEventsManager()->fire('serviceManager:initialize', $this);
        
        return $this;
    }
    
    /**
     * Route all calls to dependency injector
     *
     * @param string $name
     * @param array  $arguments
     *
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        return $this->getDI()->__call($name, $arguments);
    }
    
    /**
     * DiInjection constructor.
     *
     * @param Di|\Phalcon\DiInterface $dependencyInjector
     */
    public function __construct(Di $dependencyInjector = null)
    {
        if ($dependencyInjector)
        {
            $this->di = $dependencyInjector;
        }
        else
        {
            $this->di = Di::getDefault();
        }
    }
}
