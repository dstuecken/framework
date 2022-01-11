<?php

namespace DS\Interfaces;

use Phalcon\Config;
use Phalcon\Di;
use Phalcon\Logger;

/**
 * DS-Framework
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Interfaces
 */
interface GeneralApplication
{
    /**
     * Return config
     *
     * @return Config
     */
    public function getConfig(): Config;

    /**
     * Get Di
     *
     * @return Di
     */
    public function getDI();

    /**
     * Get root directory of the app (with ending /)
     *
     * @return string
     */
    public function getRootDirectory(): string;

    /**
     * Return current running mode.
     *
     * Can either be production, staging or development
     *
     * @return string
     */
    public function getMode(): string;

    /**
     * @param     $message
     * @param int $type
     *
     * @return GeneralApplication
     */
    public function log($message, $type = Logger::INFO): GeneralApplication;
}
