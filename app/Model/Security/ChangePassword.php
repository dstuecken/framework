<?php

namespace DS\Model\Security;

use DS\Api\Login;
use DS\Component\Mail\Events\PasswordChangedMail;
use DS\Component\ServiceManager;
use DS\Exceptions\SecurityException;
use DS\Model\User;
use DS\Traits\DiSingleton;
use Phalcon\Exception;

/**
 * Coders
 *
 * Change Password helper model, makes use of User model
 *
 * @copyright 2016/17 | dvlpr.de
 *
 * @version   $Version$
 * @package   Coders\Model
 *
 * @method static findFirstById(int $id)
 */
class ChangePassword
{
    use DiSingleton;

    /**
     * Store new password for email notifications
     *
     * @var string
     */
    private $newPassword = '';

    /**
     * Store user whos password got changed
     *
     * @var User
     */
    private $user = null;

    /**
     * Remembers the success of the password change
     *
     * @var bool
     */
    private $passwordHasBeenChanged = false;

    /**
     * @return bool
     */
    public function hasPasswordBeenChanged(): bool
    {
        return $this->passwordHasBeenChanged;
    }

    /**
     * @param User $user
     * @param      $currentPassword
     *
     * @return bool
     */
    public function verifyCurrentPassword(User $user, $currentPassword)
    {
        $login = new Login();

        return $login->verifyLogin($user->getEmail(), $currentPassword);
    }

    /**
     * @param User   $user
     * @param string $newPassword
     *
     * @return $this
     * @throws Exception
     */
    public function changePassword(User $user, string $newPassword)
    {
        $security          = ServiceManager::instance($this->getDI())->getAuth()->getSecurity();
        $this->newPassword = $newPassword;

        if ($user->setSecuritySalt($security->hash($newPassword))->save())
        {
            $this->passwordHasBeenChanged = true;
        }
        else
        {
            throw new Exception(
                'There was an error changing your password. Please try again later or contact our support.'
            );
        }

        return $this;
    }

    /**
     * Change the users password and send e-mail notification
     *
     * @param User   $user
     * @param string $currentPassword
     * @param string $newPassword
     *
     * @return $this
     */
    public function execute(User $user, $currentPassword, $newPassword)
    {
        $this->passwordHasBeenChanged = false;
        $this->user                   = $user;

        $security = ServiceManager::instance($this->getDI())->getAuth()->getSecurity();

        if ($security->checkHash($currentPassword, $user->getSecuritySalt()))
        {
            $this->changePassword($user, $newPassword);
        }
        else
        {
            throw new SecurityException('Cannot change password: Your current password is incorrect.');
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function notifiyUserAboutPasswordChange()
    {
        if ($this->user && $this->passwordHasBeenChanged)
        {
            // Send mail
            PasswordChangedMail::factory($this->getDI())
                               ->prepare($this->user)
                               ->send();
        }

        return $this;
    }

}
