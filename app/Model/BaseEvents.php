<?php

namespace DS\Model;

use DS\Component\ServiceManager;
use DS\Traits\ServiceManagerAwareTrait;
use Phalcon\Mvc\Model;

/**
 * DS-Framework
 *
 * Some default events
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright https://www.dvlpr.de
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Model
 *
 * @method static findFirstById(int $id)
 */
abstract class BaseEvents
    extends Model
{
    use ServiceManagerAwareTrait;

    /**
     * @return void
     */
    public function beforeSave()
    {
        $this->fireInstanceEvent('beforeSave');
    }

    /**
     * Fires an event with the current class instances namespace.
     *
     * E.g.: Namespace\Model:afterSave
     *
     * @param string $name
     *
     * @return void
     */
    protected function fireInstanceEvent(string $name)
    {
        $eventsManager = $this->getEventsManager() ?: $this->serviceManager->getEventsManager();
        if ($eventsManager)
        {
            $eventsManager->fire(get_class($this) . ':' . $name, $this);
        }
    }

    /**
     * @return void
     */
    public function afterSave()
    {
        $this->fireInstanceEvent('afterSave');
    }

    /**
     * @return void
     */
    public function afterCreate()
    {
        $this->fireInstanceEvent('afterCreate');
    }

    /**
     * @return void
     */
    public function beforeCreate()
    {
        $this->fireInstanceEvent('beforeCreate');
    }

    /**
     * Set timestamps and updated user id
     *
     * @return bool
     */
    public function beforeValidationOnCreate()
    {
        $time = time();

        if (property_exists($this, 'createdAt') && !$this->createdAt)
        {
            $this->createdAt = $time;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function beforeValidationOnUpdate()
    {
        return true;
    }

    /**
     * Do some checks fore saving/inserting
     */
    public function beforeValidation()
    {
        $time = time();
        if (property_exists($this, 'updatedAt'))
        {
            $this->updatedAt = $time;
        }

        if (property_exists($this, 'updatedById'))
        {
            $this->updatedById = auth()->getUserId();
        }

        return true;
    }

    /**
     * Initialize some variables for all instances
     *
     * Using onConstruct instead of initialize here since these initializations
     * should be valid for all instances of this model.
     *
     * @see https://docs.phalconphp.com/en/3.3/db-models -> onCustruct
     */
    final protected function onConstruct()
    {
        $this->initEventsAndServices();
    }

    protected function initEventsAndServices()
    {
        $this->setConnectionService('write-database');
        $this->setReadConnectionService('read-database');
        $this->setWriteConnectionService('write-database');

        $this->serviceManager = ServiceManager::instance($this->getDI());
        $this->setEventsManager($this->serviceManager->getEventsManager());
    }
}
