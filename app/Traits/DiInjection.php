<?php

namespace DS\Traits;

use DS\Component\ServiceManager;
use Phalcon\Di\DiInterface;
use Phalcon\Di\FactoryDefault;

/**
 * DS-Framework
 *
 * DI Injection trait
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component
 */
trait DiInjection
{
    /**
     * @var DiInterface
     */
    protected $di;
    
    /**
     * @var ServiceManager
     */
    protected $serviceManager;
    
    /**
     * Sets the dependency injector
     *
     * @param DiInterface $container
     */
    public function setDI(DiInterface $container): void
    {
        $this->di = $container;
    }
    
    /**
     * Returns the internal dependency injector
     *
     * @return DiInterface
     */
    public function getDI(): DiInterface
    {
        return $this->di;
    }
    
    /**
     * DiInjection constructor.
     *
     * @param DiInterface $container
     */
    public function __construct(DiInterface $container = null)
    {
        if ($container)
        {
            $this->di = $container;
        }
        else
        {
            $this->di = FactoryDefault::getDefault();
        }
        
        $this->serviceManager = ServiceManager::instance($this->di);
    }
}
