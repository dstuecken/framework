<?php

namespace DS\Controller;

use DS\Component\Links\HomeLink;
use DS\Component\ServiceManager;
use DS\Controller\Validation\ValidationSchema;
use DS\Exceptions\CsrfTokenMismatchException;
use DS\Exceptions\CsrfTokenMissingException;
use DS\Exceptions\FormValidationException;
use DS\Exceptions\PermissionDeniedException;
use DS\Exceptions\RuntimeException;
use DS\Exceptions\UserBlockedException;
use Phalcon\Exception;
use Phalcon\Mvc\Controller as PhalconMvcController;

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
abstract class BaseController
    extends PhalconMvcController
{
    /**
     * @var ServiceManager|null
     */
    protected $serviceManager = null;
    
    /**
     * @var string
     */
    protected $cacheSuffix = '';
    
    /**
     * @var string
     */
    protected $cachePrefix = '';
    
    /**
     * @var bool
     */
    protected $cacheEnabled = true;
    
    /**
     * @var string
     */
    protected $cacheKey = '';
    
    /**
     * @var string
     */
    protected $name = '';
    
    /**
     * Set this to true if the current controller should check and verify any csrf tokens
     *
     * @var bool
     */
    protected $hasCsrfToken = false;
    
    /**
     * @param string $suffix
     *
     * @return $this
     */
    protected function setCacheSuffix(string $suffix)
    {
        $this->cacheSuffix = $suffix;
        
        return $this;
    }
    
    /**
     * @return string
     */
    public function getCacheKey(): string
    {
        if (!$this->cacheKey)
        {
            $this->cacheKey = get_class($this) . '.' . $this->cachePrefix . $this->serviceManager->getAuth()->getUserId() . $this->cacheSuffix;
        }
        
        return $this->cacheKey;
    }
    
    /**
     * @param int $lifetime
     *
     * @return bool
     * @throws \Phalcon\Exception
     */
    protected function cached(int $lifetime = 120): bool
    {
        if (!$this->cacheEnabled || $this->request->has('nocache') || Application::instance()->getMode() === 'development')
        {
            return false;
        }
        
        // todo: Caching currently not implemented.
        return false;
        
        /*
        $viewCache = $this->serviceManager->getViewCache();
        $cacheKey  = $this->getCacheKey();
        if ($viewCache->has($cacheKey))
        {
            $viewCache->get($cacheKey);
            
            return true;
        }
        else
        {
            $viewCache->set($cacheKey, $this->view->getContent());
        }
        $this->view->cache(
            [
                "lifetime" => $lifetime,
                "key" => $this->getCacheKey(),
            ]
        );
        
        return $this->view->getCache()->exists($this->getCacheKey());
        */
    }
    
    /**
     * Fires an event that can be responsible for checking the users auth roles.
     *
     * @param array $anyRoles
     * @param       $redirectToHome
     *
     * @return bool
     * @throws RuntimeException
     */
    protected function authorizationCheck(array $anyRoles, $redirectToHome = true): bool
    {
        if (!$this->serviceManager)
        {
            throw new RuntimeException('ServiceManager is not initialized.');
        }
        
        $this->eventsManager->fire('auth:authorizationCheck', $anyRoles, $redirectToHome);
        
        return true;
    }
    
    /**
     * @param ValidationSchema $schema
     * @param bool             $throwException
     *
     * @return \Phalcon\Validation\Message\Group
     * @throws FormValidationException
     */
    protected function validate(ValidationSchema $schema, bool $throwException = false)
    {
        return $this->validateData($this->request->getPost(), $schema, $throwException);
    }
    
    /**
     * @param array            $data
     * @param ValidationSchema $schema
     * @param bool             $throwException
     *
     * @return \Phalcon\Validation\Message\Group
     * @throws FormValidationException
     */
    protected function validateData(array $data, ValidationSchema $schema, bool $throwException = false)
    {
        $errors = $schema->validate($data);
        if (count($errors))
        {
            $userErrors = [];
            foreach ($errors as $error)
            {
                $userErrors[$error->getField()] = $error->getMessage();
            }
            
            $this->view->setVar('errors', $userErrors);
            if ($throwException)
            {
                throw new FormValidationException(implode("\n", $userErrors), $userErrors);
            }
        }
        
        return $errors;
    }
    
    /**
     * @param ServiceManager $serviceManager
     *
     * @return $this
     */
    public function setServiceManager($serviceManager)
    {
        $this->serviceManager = $serviceManager;
        
        return $this;
    }
    
    /**
     * @throws CsrfTokenMismatchException
     * @throws CsrfTokenMissingException
     */
    protected function verifyCsrfToken()
    {
        if (!$this->request->isPost())
        {
            return;
        }
        
        if (!$this->security->getSessionToken())
        {
            $this->serviceManager->getMixpanel()->track('Login.CsrfTokenMissing', ['post' => json_encode($this->request->getPost())]);
            
            throw new CsrfTokenMissingException(
                'Request Error. We detected a security issue with your browser request. Please try again later. (101)'
            );
        }
        
        if ($this->security->checkToken())
        {
            $this->security->destroyToken();
            
            return;
        }
        
        $this->security->destroyToken();
        $this->serviceManager->getMixpanel()->track('Login.CsrfTokenMismatch', ['post' => json_encode($this->request->getPost())]);
        
        throw new CsrfTokenMismatchException(
            'Request Error. We detected a security issue with your browser request. Please try again later. (103)'
        );
    }
    
    /**
     * Initialize Service Manager
     */
    public function onConstruct()
    {
        if ($this->serviceManager === null)
        {
            $this->serviceManager = ServiceManager::instance($this->di);
        }
    }
    
    /**
     * Initialize controller
     *
     * @return $this
     * @throws Exception
     * @throws CsrfTokenMismatchException
     * @throws CsrfTokenMissingException
     * @throws UserBlockedException
     */
    public function initialize()
    {
        $auth = $this->serviceManager->getAuth();
        
        // Providing the instance to our view
        if (!isset($this->view->auth))
        {
            $this->view->setVar('auth', $auth);
        }
        
        $this->view->setVar('pageName', 'Unnamed Page');
        
        // CSRF Handling
        if ($this->hasCsrfToken)
        {
            try
            {
                $this->verifyCsrfToken();
            }
            catch (CsrfTokenMismatchException $e)
            {
                sentryException($e);
                $this->flashSession->error($e->getMessage());
                header('Location: ' . HomeLink::get($this->request->getURI()));
                die;
            }
            catch (CsrfTokenMissingException $e)
            {
                sentryException($e);
                $this->flashSession->error($e->getMessage());
                header('Location: ' . HomeLink::get($this->request->getURI()));
                die;
            }
        }
        
        return $this;
    }
}
