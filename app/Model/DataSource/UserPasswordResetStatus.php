<?php

namespace DS\Model\DataSource;

/**
 *
 * DS-Framework
 *
 * ENUM status of User status
 *
 * @copyright 2017 | DS
 *
 * @version   $Version$
 * @package   DS\Model\DataSource
 */
class UserPasswordResetStatus
{
    /**
     * Change request is pending
     */
    const Pending = 0;
    
    /**
     * Has been changed
     */
    const Changed = 1;
    
    /**
     * Timeout
     */
    const TimedOut = 2;
}
