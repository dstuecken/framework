<?php
namespace DS\Component\Session;

use Phalcon\Session\Manager as PhalconSessionManager;

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
 */
class Manager extends PhalconSessionManager
{
    /**
     * @return bool
     */
    public function isStarted(): bool
    {
        return 1 === $this->status() || 2 === $this->status();
    }
    
}
