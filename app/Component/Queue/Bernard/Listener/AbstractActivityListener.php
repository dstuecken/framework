<?php

namespace DS\Component\Queue\Bernard\Listener;

use Bernard\Message;
use Bernard\Receiver;
use DS\Component\Queue\ListenerInterface;
use DS\Component\Queue\TriggerInterface;
use Phalcon\Di;
use Phalcon\Di\Injectable;

/**
 * DS-Framework
 *
 * Queueing
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component\Mail
 */
abstract class AbstractActivityListener extends Injectable implements ListenerInterface, Receiver
{

    /**
     * @var Receiver[]
     */
    protected $map = [];

    /**
     * @return string
     */
    abstract public static function getConsumerName(): string;

    /**
     * @return array
     */
    public function getMap(): array
    {
        return $this->map;
    }

    /**
     * @param Message\PlainMessage $message
     *
     * @todo Logging
     */
    public function receive(Message $message)
    {
        if ($message->has('trigger'))
        {
            if (isset($this->map[$message->get('trigger')]))
            {
                // Route message to the ActivityListener
                $this->map[$message->get('trigger')]->receive(
                    new Message\PlainMessage(
                        $message->get('trigger'),
                        $message->get('data')
                    )
                );
            }
        }
    }

    /**
     * Initialize User Activity Listener
     *
     * @return mixed|void
     */
    public function initialize(): void
    {
        $namespace    = substr(get_called_class(), 0, strrpos(get_called_class(), '\\'));
        $activityName = substr($namespace, strrpos($namespace, '\\') + 1);

        // Iterate through activity classes and initialize a queue listener for each
        $directory = __DIR__ . '/' . $activityName . '/Activity/';

        $this->loadActivityListeners($directory, $namespace . '\Activity');
    }

    /**
     * Load all activity listeners from $directory
     *
     * @param string $directory
     * @param string $namespace
     */
    protected function loadActivityListeners(string $directory, string $namespace): array
    {
        if ($dirhandle = opendir($directory))
        {
            while (($file = readdir($dirhandle)) !== false)
            {
                if ($file === '.' || $file === '..')
                {
                    continue;
                }

                $className = $namespace . '\\' . str_replace('.php', '', $file);

                /**
                 * Add Listener to map
                 *
                 * @var $className TriggerInterface
                 */
                if (is_a($className, TriggerInterface::class, true))
                {
                    $this->map[$className::getTrigger()] = new $className($this->di);
                }
            }
        }

        return $this->map;
    }

    /**
     * DiInjection constructor.
     *
     * @param Di|\Phalcon\DiInterface $dependencyInjector
     */
    public function __construct(Di $dependencyInjector = null)
    {
        $this->setDI($dependencyInjector);
        $this->initialize();
    }
}
