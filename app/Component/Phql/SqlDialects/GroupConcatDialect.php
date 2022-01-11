<?php

namespace DS\Component\Phql\SqlDialects;

use DS\Component\Phql\SqlDialect;
use Phalcon\Db\Adapter\Pdo\MySQL as Connection;

/**
 * DS-Framework
 *
 * Queueing
 *
 * @author Dennis Stücken
 * @license proprietary

 * @copyright Dennis Stücken
 * @link https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component\Mail
 */
class GroupConcatDialect
{
    /**
     * @return SqlDialect
     */
    public static function register()
    {
        $dialect = SqlDialect::instance();
        
        // Register a new function called MATCH_AGAINST
        $dialect->registerCustomFunction(
            'CUSTOM_GROUP_CONCAT',
            function (SqlDialect $dialect, array $expression)
            {
                $arguments = $expression['arguments'];
                
                return sprintf(
                    "GROUP_CONCAT(%s ORDER BY %s %s SEPARATOR '%s')",
                    $dialect->getSqlExpression($arguments[0]),
                    $dialect->getSqlExpression($arguments[1]),
                    isset($arguments[2]) ? trim($arguments[2]['value'], '\'') : 'ASC',
                    isset($arguments[3]) ? trim($arguments[3]['value'], '\'') : ', '
                );
            }
        );
        
        return $dialect;
    }
}
