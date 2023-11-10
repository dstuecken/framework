<?php

namespace DS\Traits;

use DS\Component\ServiceManager;

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
trait ServiceManagerAwareTrait
{

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @return ServiceManager
     */
    public function getServiceManager(): ServiceManager
    {
        return $this->serviceManager;
    }

    /**
     * @param ServiceManager $serviceManager
     * @return self
     */
    public function setServiceManager(ServiceManager $serviceManager): self
    {
        $this->serviceManager = $serviceManager;

        return $this;
    }


}
