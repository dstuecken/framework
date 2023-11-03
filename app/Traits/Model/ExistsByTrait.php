<?php

namespace DS\Traits\Model;

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
 *
 * @method static self findFirst(array $parameters)
 */
trait ExistsByTrait
{
    /**
     * @param string $slug
     *
     * @return self|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function existsBy(string $field, string $value)
    {
        return self::findFirst(
            [
                "conditions" => "{$field} = ?0",
                "bind" => [$value],
                "limit" => 1,
            ]
        );
    }
}
