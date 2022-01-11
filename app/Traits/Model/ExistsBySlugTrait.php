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
trait ExistsBySlugTrait
{
    /**
     * @param string $slug
     *
     * @return self|\Phalcon\Mvc\Model\ResultInterface
     */
    public static function exists(string $slug)
    {
        return self::findFirst(
            [
                "conditions" => "slug = ?0",
                "bind" => [$slug],
                "limit" => 1,
            ]
        );
    }
}
