<?php

namespace DS\Component\Analytics;

use DS\Application;
use Phalcon\Exception;
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
 * @package   DS\Component
 */
class MixpanelLoggingConsumer extends \ConsumerStrategies_AbstractConsumer
{
    /**
     * @var array
     */
    private $logged = [];
    
    /**
     * @param array $batches
     *
     * @return bool|void
     */
    public function persist($batches)
    {
        foreach ($batches as $batch)
        {
            if (!isset($batch['event']))
            {
                continue;
            }
            
            if (!isset($this->logged[$batch['event']]))
            {
                try
                {
                    Application::instance()->log('Mixpanel: ' . $batch['event'] . ' ' . json_encode($batch['properties']) . '', Logger::DEBUG);
                    $this->logged[$batch['event']] = true;
                }
                catch (Exception $e)
                {
                    // Application might not be initialized
                }
            }
        }
    }
}
