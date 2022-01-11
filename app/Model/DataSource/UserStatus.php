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
class UserStatus
{
    /**
     * If dataset was created and not confirmed / inactive
     */
    const Unconfirmed = 0;

    /**
     * Normal state for a dataset
     */
    const Confirmed = 1;

    /**
     * Onboarding process not done, yet
     */
    const Active = 2;

    /**
     * Inactive / hidden dataset
     */
    const Deleted = 3;

    /**
     * Blocked from using thesite
     */
    const Blocked = 4;
}
