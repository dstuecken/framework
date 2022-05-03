<?php

namespace DS\Component;

use DS\Traits\EventsAwareTrait;
use Phalcon\Di;
use Phalcon\Di\AbstractInjectionAware;
use Phalcon\Di\FactoryDefault;
use Phalcon\Events\EventsAwareInterface;
use Phalcon\Events\Manager;
use Phalcon\Security;
use Phalcon\Session\ManagerInterface;

/**
 * DS-Framework
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component
 *
 * @property ManagerInterface $session
 */
class Auth
    extends AbstractInjectionAware
    implements EventsAwareInterface
{
    use EventsAwareTrait;
    
    /**
     * Id of current user
     *
     * @var int|null
     */
    public $userId = null;
    
    /**
     * User roles
     *
     * @var int
     */
    protected $roles = 0;
    
    /**
     * Remember authenticated session for 15 days
     *
     * @var int
     */
    public $rememberForDays = 15;
    
    /**
     * Return internal phalcon security component
     *
     * @return \Phalcon\Security
     */
    public function getSecurity()
    {
        return new Security($this->getSession(), ServiceManager::instance($this->getDI())->getRequest());
    }
    
    /**
     * @return int
     */
    public function getRememberForDays(): int
    {
        return $this->rememberForDays;
    }
    
    /**
     * @param int $rememberForDays
     *
     * @return $this
     */
    public function setRememberForDays($rememberForDays): Auth
    {
        $this->rememberForDays = $rememberForDays;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }
    
    /**
     * @param int $userId
     *
     * @return $this
     */
    public function setUserId($userId): Auth
    {
        $this->userId = (int) $userId;
        
        return $this;
    }
    
    /**
     * @return int
     */
    public function getRoles(): int
    {
        return $this->roles;
    }
    
    /**
     * @param int $roles
     *
     * @return $this
     */
    public function setRoles($roles)
    {
        $this->roles = $roles;
        
        return $this;
    }
    
    /**
     * @return $this
     */
    public function logout(): Auth
    {
        // Clear hybridauth session
        //$redisStorage = new RedisStorage($this->session);
        //$redisStorage->clear();
        
        // Remove user from internal auth store
        $this->userId = null;
        
        // Remove user id explicitly just to be sure
        $this->session->remove('uid');
        
        return $this;
    }
    
    /**
     * Remove session
     *
     * @return $this
     */
    public function removeSession(): Auth
    {
        try
        {
            $this->logout();
            
            // Then remove everything else
            $this->session->destroy();
            
            // Remove session token from cookies
            ServiceManager::instance()->getCookies()->set('sessToken', '')->send();
            
            // This should fix "session_regenerate_id(): Session object destruction failed. ID: user (path: )"
            // Phalcon 3.0.x may fixed this as well: https://github.com/phalcon/cphalcon/pull/12206 - leaving the if condition commented for now
            // if ($this->session->isStarted() && $this->session->status() === SessionAdapter::SESSION_ACTIVE)
            {
                // Generate new session id
                $this->session->regenerateId(true);
            }
        }
        catch (\Exception $e)
        {
            application()->log($e->getMessage());
        }
        
        return $this;
    }
    
    /**
     * @return ManagerInterface
     */
    public function getSession(): ManagerInterface
    {
        return $this->session;
    }
    
    /**
     * Session token
     *
     * @return string
     */
    public function getSessionToken(): string
    {
        return $this->session->getId();
    }
    
    /**
     * User is logged in
     */
    public function loggedIn(): bool
    {
        return !!$this->userId;
    }
    
    /**
     * Update last login date of the user that has logged in
     *
     * @return $this
     */
    public function updateLastLogin(): Auth
    {
        if ($this->user)
        {
            $this->user->setLastLogin(time())->save();
        }
        
        return $this;
    }
    
    /**
     * Auth constructor.
     *
     * @param FactoryDefault $di
     */
    public function __construct(FactoryDefault $di)
    {
        $this->setDI($di);
        $this->session = $this->getDI()->get('session');
        
        if (isset($this->cookies) && $this->cookies)
        {
            $this->cookies->useEncryption(true);
        }
        
        if (!$this->session->isStarted())
        {
            if (!$this->session->start())
            {
                ServiceManager::instance($this->getDI())->getLogger()->warning(sprintf('Could not start session (%s)', $this->getSessionToken()));
            }
        }
        
        if ($this->session->isStarted())
        {
            // This is needed for checking if a user is logged in or not
            $this->userId = (int) $this->session->get('uid');
        }
        else
        {
            $this->userId = null;
        }
        
        $this->getEventsManager()->fire('auth:sessionStarted', $this);
    }
}
