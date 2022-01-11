<?php

namespace DS\Task\TaskHelpers;

use DS\CliApplication;

/**
 * DS-Framework
 *
 * Helper Task to enable logging within takss
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Model
 */
trait OnlyAllowOneInstanceTrait
{
    /**
     * Exits if there is another instance of this process running
     */
    protected function onlyAllowOneInstance(CliApplication $app): void
    {
        php_sapi_name() === 'cli' || exit;
        
        $argv = $app->getCliArguments();
        
        if (isset($argv[1]))
        {
            substr_count(shell_exec('ps -ax'), $argv[0] . " " . $argv[1]) <= 1 || ($app->log("{$argv[1]} already running.. Exiting.") && exit);
        }
    }
}
