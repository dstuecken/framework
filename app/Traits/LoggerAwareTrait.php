<?php
namespace DS\Traits;

use Phalcon\Logger\AdapterInterface;

/**
 * DS-Framework Application
 *
 * @author Dennis Stücken
 * @license proprietary

 * @copyright Dennis Stücken
 * @link https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Controller
 */
trait LoggerAwareTrait
{
    /**
     * The logger instance.
     *
     * @var AdapterInterface
     */
    protected $logger;

    /**
     * Sets a logger.
     *
     * @param AdapterInterface $logger
     *
     * @return $this
     */
    public function setLogger(AdapterInterface $logger)
    {
        $this->logger = $logger;

        return $this;
    }
}
