<?php

namespace DS\Component\Phql;

use DS\Traits\Singleton;
use Phalcon\Db\Dialect\Mysql;

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
class SqlDialect extends Mysql
{
    use Singleton;
}
