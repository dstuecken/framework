<?php

namespace DS\Component;

use DS\Model\Abstracts\AbstractUser;
use DS\Model\DataSource\UserRoles;
use DS\Model\Stripe;
use DS\Model\User;
use DS\Model\UserSettings;
use Phalcon\Di\AbstractInjectionAware;
use Phalcon\Di\FactoryDefault;
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
{
    
    /**
     * Id of current user
     *
     * @var int
     */
    public $userId;
    
    /**
     * User Model
     *
     * @var User
     */
    protected $user;
    
    /**
     * @var Stripe
     */
    protected $stripe;
    
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
     * @return bool
     */
    public function hasAccess(): bool
    {
        return $this->user && $this->user->hasRole(UserRoles::Access);
    }
    
    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->user && $this->user->hasRole(UserRoles::Admin);
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
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
    
    /**
     * @return int
     */
    public function getRoles(): int
    {
        return $this->roles;
    }
    
    /**
     * @param User $user
     *
     * @return $this
     */
    public function setUser(User $user): Auth
    {
        $this->userId = (int) $user->getId();
        $this->user   = $user;
        
        return $this;
    }
    
    /**
     * Load user object
     *
     * @return Auth
     */
    private function loadUser(): Auth
    {
        if ($this->userId > 0)
        {
            $this->user = User::findFirstById($this->userId);
            if (!$this->user)
            {
                $this->user   = null;
                $this->userId = 0;
                
                return $this;
                
            }
            
            // and user roles
            $this->roles = $this->user->getRoles();
            
            // Identify mixpanel user
            ServiceManager::instance($this->getDI())->getMixpanel()->identify($this->user);
        }
        else
        {
            $this->user   = null;
        }
        
        // onUserLoaded Hook:
        if (method_exists($this, 'onUserLoaded')) {
            $this->onUserLoaded();
        }
        
        return $this;
    }
    
    /**
     * Login
     *
     * @param AbstractUser $user
     *
     * @return $this
     */
    public function storeSession(AbstractUser $user): Auth
    {
        try
        {
            $this->session->remove('uid');
            $this->session->set('uid', $user->getId());
            
            //$this->session->regenerateId(true);
            
            if ($user->getLastSessionId())
            {
                // Set session to last session id so that the old session gets removed
                // $this->session->setId($user->getLastSessionId());
            }
            
            // $this->removeSession();
            
            // Set user for internal usage
            $this->userId = (int) $user->getId();
            
            // Store current user id in session
            $this->session->set('uid', $this->userId);
            
            // Set last session id, store user in member variable and push user's new session id to db
            $this->user = $user->setLastSessionId($this->session->getId());
            $this->user->save();
            
            // Identify mixpanel user
            ServiceManager::instance($this->getDI())->getMixpanel()->identify($this->user);
        }
        catch (\Exception $e)
        {
            application()->log($e->getMessage());
        }
        
        return $this;
    }
    
    public function logout(): Auth
    {
        // Clear hybridauth session
        //$redisStorage = new RedisStorage($this->session);
        //$redisStorage->clear();
        
        // Remove user from internal auth store
        $this->userId = 0;
        $this->user   = null;
        
        // Remove user id explicitly just to be sure
        $this->session->remove('uid');
        
        return $this;
    }
    
    /**
     * Logout
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
            // @see https://sentry.io/coders/coders/issues/243834480/
            // Phalcon 3.0.x may fixed this as well: https://github.com/phalcon/cphalcon/pull/12206 so i am leaving the if commented for now
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
        return ($this->userId > 0 && $this->user);
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
            //$this->session->setName('tdSession');
            if (!$this->session->start())
            {
                ServiceManager::instance($this->getDI())->getLogger()->warning(sprintf('Could not start session (%s)', $this->getSessionToken()));
            }
        }
        
        if ($this->session->isStarted())
        {
            // This is needed for checking if a user is logged in or not
            $this->userId = (int) $this->session->get('uid');
            $this->loadUser();
        }
        else
        {
            $this->userId = 0;
        }
    }
}
