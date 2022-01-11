<?php

namespace DS\Model;

use DS\Component\Cache\Redis;
use DS\Component\ServiceManager;
use Phalcon\Db\ResultInterface;
use Phalcon\Di;
use Phalcon\Escaper;

/**
 * DS-Framework
 *
 * Base model. Used for further method rollouts.
 *
 * @author             Dennis Stücken
 * @license            proprietary
 * @copyright https://www.dvlpr.de
 * @link               https://www.dvlpr.de
 *
 * @version            $Version$
 * @package            DS\Model
 *
 * @method static findFirstById(int $id)
 */
abstract class Base
    extends BaseEvents
{
    /**
     * @var ServiceManager
     */
    protected $serviceManager;
    
    /**
     * @var static[]
     */
    protected static $getCache = [];
    
    /**
     * @return static
     */
    public function beginTransaction()
    {
        $manager     = $this->serviceManager->getTransactions();
        $transaction = $manager->get(true)
                               ->throwRollbackException(true);
        
        $this->setTransaction($transaction);
        
        return $this;
    }
    
    /**
     * @return static
     */
    public function commitTransaction()
    {
        if ($this->getTransaction())
        {
            $this->getTransaction()->commit();
        }
        
        return $this;
    }
    
    /**
     * @return static
     */
    public function rollbackTransaction()
    {
        if (!$this->isTransactionActive())
        {
            return $this;
        }
        
        if ($this->getTransaction())
        {
            $this->getTransaction()->rollback();
        }
        
        return $this;
    }
    
    /**
     * @return bool
     */
    public function isTransactionActive(): bool
    {
        if ($this->getTransaction())
        {
            $this->getTransaction()->isValid();
        }
        
        return false;
    }
    
    /**
     * @param string $column
     * @param string $id
     *
     * @return bool
     */
    public function deleteByFieldValue(string $column = 'id', string $id): bool
    {
        return $this->getWriteConnection()->delete($this->getSource(), sprintf("%s = ?", $column), [$id]);
    }
    
    /**
     * @param string $name
     *
     * @return mixed
     */
    public function getEscapedField(string $name)
    {
        if (isset($this->$name))
        {
            $escaper = new Escaper();
            
            return preg_replace("/[\n\r]/", "", (trim(nl2br($escaper->escapeHtmlAttr($this->$name), true))));
        }
        
        return null;
    }
    
    /**
     * Return model instance by id
     *
     * @param        $id
     * @param string $column
     *
     * @return static
     */
    public static function get($id, string $column = 'id')
    {
        if (property_exists(static::class, $column))
        {
            return static::findFirst(
                [
                    "conditions" => sprintf("%s = ?0", $column),
                    "bind" => [$id],
                ]
            ) ?: new static();
        }
        throw new \InvalidArgumentException('Invalid field name provided. This field is not available in this model.');
    }
    
    /**
     * Checks if current object exists (id > 0)
     *
     * @return bool
     */
    public function isExisting(): bool
    {
        if (!method_exists($this, 'getId'))
        {
            return false;
        }
        
        return $this->getId() > 0;
    }
    
    /**
     * Return model instance by id
     * Makes use of cache
     *
     * @param        $id
     * @param string $column
     *
     * @return static
     */
    public static function getCached($id, string $column = 'id')
    {
        if (property_exists(static::class, $column))
        {
            return static::findFirstCached(
                [
                    "conditions" => sprintf("%s = ?0", $column),
                    "bind" => [$id],
                ]
            ) ?: new static();
        }
        throw new \InvalidArgumentException('Invalid field name provided. This field is not available in this model.');
    }
    
    /**
     * Allows to query this model by the given sql field name and it's value
     *
     * @param string $field
     * @param string $value
     *
     * @return static
     */
    public static function findByFieldValue($field, $value)
    {
        if (property_exists(static::class, $field))
        {
            return static::findFirst(
                [
                    "conditions" => sprintf("%s = ?0", $field),
                    "limit" => 1,
                    "bind" => [$value],
                ]
            );
        }
        
        throw new \InvalidArgumentException('Invalid field name provided. This field is not available in this model.');
    }
    
    /**
     * @param        $field
     * @param        $value
     * @param string $orderColumn
     *
     * @return static
     */
    public static function findLatestByFieldValue($field, $value, $orderColumn = 'createdAt')
    {
        if (property_exists(static::class, $field))
        {
            return static::findFirst(
                [
                    "conditions" => sprintf("%s = ?0", $field),
                    "limit" => 1,
                    "order" => $orderColumn . " DESC",
                    "bind" => [$value],
                ]
            );
        }
        
        throw new \InvalidArgumentException('Invalid field name provided. This field is not available in this model.');
    }
    
    /**
     * Allows to query this model by the given sql field name and it's value
     *
     * @param $field
     * @param $value
     *
     * @return static[]
     */
    public static function findAllByFieldValue($field, $value)
    {
        if (property_exists(static::class, $field))
        {
            return static::find(
                [
                    "conditions" => sprintf("%s = ?0", $field),
                    "bind" => [$value],
                ]
            );
        }
        
        throw new \InvalidArgumentException('Invalid field name provided. This field is not available in this model.');
    }
    
    /**
     * @param int    $limit
     * @param string $order
     *
     * @return static[]
     */
    public static function findWithLimit($limit, $order = null)
    {
        return static::find(
            [
                "limit" => $limit,
                "order" => $order,
            ]
        );
    }
    
    /**
     * $id is the id value of current table. ( = findFirstById is called)
     *
     * @param int $id
     *
     * @return $this
     */
    public static function getInstance($id = null)
    {
        $instance = null;
        if ($id)
        {
            $instance = self::findFirstById($id);
        }
        
        if (!$instance)
        {
            $instance = new static;
            $instance->initialize();
        }
        
        return $instance;
    }
    
    /*
     * Still unsure about this feature
     * It's maybe better implemented via Decorator Pattern for the Query-Builder
     *
    protected function applyLimit(Limit $limit = null)
    {
        if ($limit)
        {
            //$this->limit($limit->getLimit(), $limit->getOffset());
        }
    }
    */
    
    /**
     * Prepare cacheble key for parameters array
     *
     * @param array $parameters
     *
     * @return array
     */
    private static function prepareCacheParameters(array $parameters = []): array
    {
        // Convert the parameters to an array
        if (!is_array($parameters))
        {
            $parameters = [$parameters];
        }
        
        // Check if a cache key wasn't passed
        // and create the cache parameters
        if (!isset($parameters["cache"]))
        {
            $parameters["cache"] = [
                "key" => self::createKey($parameters),
                "lifetime" => 14400,
            ];
        }
        
        return $parameters;
    }
    
    /**
     * Implement a method that returns a string key based
     * on the query parameters
     */
    private static function createKey($parameters)
    {
        $uniqueKey = [];
        
        foreach ($parameters as $key => $value)
        {
            if (is_scalar($value))
            {
                $uniqueKey[] = $key . "_" . $value;
            }
        }
        
        return static::class . '_' . implode(",", $uniqueKey);
    }
    
    /**
     * Auto cache for find()
     *
     * @param null $parameters
     *
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public static function findCached($parameters = null)
    {
        return parent::find(self::prepareCacheParameters($parameters));
    }
    
    /**
     * Auto cache for findFirst()
     *
     * @param null $parameters
     *
     * @return \Phalcon\Mvc\Model
     */
    public static function findFirstCached($parameters = null)
    {
        return parent::findFirst(self::prepareCacheParameters($parameters));
    }
    
    /*
     * Returns related records based on defined relations
     *
     * @param string alias
     * @param array arguments
     * @return \Phalcon\Mvc\Model\ResultsetInterface
     */
    public function getRelated($alias, $arguments = null)
    {
        return parent::getRelated(
            $alias,
            array_merge_recursive(
                $arguments,
                [
                    'cache' => [
                        'key' => 'modelsCache.' . $alias,
                        'lifetime' => 84600,
                    ],
                ]
            )
        );
    }
    
    /**
     * @return Redis
     */
    public function getCache()
    {
        return ServiceManager::instance()->getRedis();
    }
    
    /**
     * Sends SQL statements to the read database server returning the success state.
     *
     * @param string $sqlStatement
     * @param mixed  $placeholders
     * @param mixed  $dataTypes
     *
     * @return bool|ResultInterface
     */
    public function readQuery($sqlStatement, $placeholders = null, $dataTypes = null)
    {
        return $this->getReadConnection()->query($sqlStatement, $placeholders, $dataTypes);
    }
    
    /**
     * Sends SQL statements to the write database server returning the success state.
     *
     * @param string $sqlStatement
     * @param mixed  $placeholders
     * @param mixed  $dataTypes
     *
     * @return bool|ResultInterface
     */
    public function writeQuery($sqlStatement, $placeholders = null, $dataTypes = null)
    {
        return $this->getWriteConnection()->query($sqlStatement, $placeholders, $dataTypes);
    }
    
    /**
     * Checks if snapshot data has been changed
     *
     * @param string $field
     *
     * @return bool
     */
    public function snapshotDataChanged(string $field): bool
    {
        return $this->hasSnapshotData() && isset($this->$field) && ($this->getSnapshotData()[$field] != $this->$field);
    }
    
    /**
     * @param Di|null $di
     *
     * @return static
     */
    public static function factory(Di $di = null)
    {
        return new static($di);
    }
    
    /**
     * Initialize some variables for all instances
     *
     * Using onConstruct instead of initialize here since these initializations should be valid for all instances of this model.
     *
     * @see https://docs.phalconphp.com/en/3.3/db-models -> onCustruct
     */
    protected final function onConstruct()
    {
        $this->setConnectionService('write-database');
        $this->setReadConnectionService('read-database');
        $this->setWriteConnectionService('write-database');
        
        $this->serviceManager = ServiceManager::instance($this->getDI());
    }
    
    /**
     * Initialize method for this model
     */
    public function initialize()
    {
    
    }
}
