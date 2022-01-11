<?php

namespace DS\Events;

/**
 * DS-Framework
 *
 * Events like views, changes or contributions
 * Used to distribute and canalize all actions that are associated with a table
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Events\Table
 */
class AbstractEvent
{
    /**
     * @return \Phalcon\Di
     */
    protected static function getDi(): \Phalcon\Di
    {
        return di();
    }
}
