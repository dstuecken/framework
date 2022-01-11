<?php
/**
 * DS-Framework
 *
 * Global Transactions
 *
 * @package DS
 * @version $Version$
 */

use Phalcon\Mvc\Model\Transaction\Manager as TransactionManager;

return function (\DS\Interfaces\GeneralApplication $application, Phalcon\Di\FactoryDefault $di)
{
    $di->setShared(
        'transactions',
        function () use($di)
        {
            return (new TransactionManager($di))->setDbService('write-database');
        }
    );
};