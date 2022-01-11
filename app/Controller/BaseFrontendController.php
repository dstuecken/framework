<?php

namespace DS\Controller;

use Phalcon\Exception;

/**
 * DS-Framework
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.rarelytics.com
 *
 * @version   $Version$
 * @package   DS\Controller
 */
abstract class BaseFrontendController
    extends BaseController
{
    /**
     * @return BaseController
     */
    protected function disableBreadcrumbs(): BaseController
    {
        $this->view->setVar('disableBreadcrumbs', true);
        
        return $this;
    }
    
    /**
     * @param string $name
     * @param string $link
     *
     * @return $this
     */
    public function addBreadcrumb(string $name, string $link = '')
    {
        $this->serviceManager->getBreadcrumbs()->add($name, $link);
        
        return $this;
    }
    
    /**
     * @param string $name
     * @param string $link
     *
     * @return $this
     */
    public function setName(string $name, string $link = '')
    {
        $this->name = $name;
        $this->view->setVar('pageName', $name);
        
        if ($link)
        {
            $this->serviceManager->getBreadcrumbs()->add($name, $link);
        }
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    
    /**
     * Redirect to 404 not found controller.
     */
    protected function redirectNotFound()
    {
        $this->dispatcher->forward(
            [
                "controller" => "Error",
                "action" => "notFound",
            ]
        );
    }
}
