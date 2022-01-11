<?php

namespace DS\Traits\Api;

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
trait GetAllTrait
{
    
    /**
     * Default columns to use for response
     *
     * @var string
     */
    private static $defaultColumns = 'id, title';
    
    /**
     * Return all locations
     *
     * @return array
     */
    public static function getAll(): array
    {
        if (isset(self::$modelClass))
        {
            $class = '\\DS\\Model\\' . self::$modelClass;
            if ($class && class_exists($class))
            {
                /**
                 * \DS\Model\Base $model
                 */
                $model = new $class;
                
                return $model->find(
                    [
                        'columns' => self::$defaultColumns,
                    ]
                )->toArray();
            }
        }
        
        return [];
    }
}
