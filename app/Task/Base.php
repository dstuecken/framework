<?php

namespace DS\Task;

use DS\Cli\Interaction\Input;
use DS\Cli\Interaction\Output;
use DS\CliApplication;
use DS\Component\ServiceManager;
use Phalcon\Cli\Task;

/**
 * DS-Framework
 *
 * GrabFeeds Task
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Model
 */
abstract class Base
    extends Task
{
    
    /**
     * The command name
     *
     * @var string
     */
    protected $name;
    
    /**
     * The command description
     *
     * @var string
     */
    protected $description;
    
    /**
     * @var Output
     */
    protected $output;
    
    /**
     * @var Input
     */
    protected $input;
    
    /**
     * @var CliApplication
     */
    protected $app;
    
    /**
     * @var ServiceManager
     */
    protected $serviceManager;
    
    /**
     * @var \Blackfire\Client
     */
    protected $blackfire = null;
    
    /**
     * @var \Blackfire\Probe
     */
    protected $probe = null;
    
    /**
     * @var \Blackfire\Profile
     */
    protected $profile = null;
    
    /**
     * Start blackfire profile
     *
     * @param string $title
     */
    protected function startProfiling($title = ''): void
    {
        if (null === $this->blackfire)
        {
            $this->blackfire = new \Blackfire\Client();
        }
        
        $config = new \Blackfire\Profile\Configuration();
        $config->setTitle($title);
        
        $this->probe = $this->blackfire->createProbe($config);
    }
    
    /**
     * @return static
     */
    public static function factory()
    {
        $self = new static();
        $self->initialize();
        
        return $self;
    }
    
    /**
     * End blackfire profile
     */
    protected function endProfiling(): void
    {
        if (null === $this->probe)
        {
            return;
        }
        
        $this->profile = $this->blackfire->endProbe($this->probe);
    }
    
    /**
     * @param string $text
     * @param bool   $newline
     *
     * @return $this
     */
    public function debugOutput($text, $newline = false): Base
    {
        if ($this->app->isDebug())
        {
            if ($newline)
            {
                $this->getOutput()->writeln($text);
            }
            else
            {
                $this->getOutput()->write($text);
            }
        }
        else
        {
            $this->logger->debug($text);
        }
        
        return $this;
    }
    
    /**
     * Returns the output instance
     *
     * @return Output
     */
    public function getOutput(): Output
    {
        return $this->output;
    }
    
    /**
     * Returns the input instance
     *
     * @return Input
     */
    public function getInput(): Input
    {
        return $this->input;
    }
    
    /**
     * Destructor ends profiling (if enabled)
     */
    public function __destruct()
    {
        // End profiler (if enabled)
        $this->endProfiling();
    }
    
    /**
     * @throws \Phalcon\Exception
     */
    public function initialize()
    {
        $this->logger = $this->getDI()->get('logger');
        $this->input  = new Input();
        $this->output = new Output();
        
        $this->app            = CliApplication::instance();
        $this->serviceManager = $this->app->getServiceManager();
        
        if (method_exists($this, 'onlyAllowOneInstance'))
        {
            $this->onlyAllowOneInstance($this->app);
        }
    }
}
