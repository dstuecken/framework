<?php

namespace DS\Model\Events;

use DS\Model\Abstracts\AbstractCities;

/**
 * Events for model Cities
 *
 * @see https://docs.phalconphp.com/ar/3.2/db-models-events
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright https://www.dvlpr.de
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Model
 */
abstract class CitiesEvents
    extends AbstractCities
{
    
    /**
     * @return bool
     */
    public function beforeValidationOnCreate()
    {
        parent::beforeValidationOnCreate();
        
        return true;
    }
    
    /**
     * @return bool
     */
    public function beforeValidationOnUpdate()
    {
        parent::beforeValidationOnUpdate();
        
        return true;
    }
}
