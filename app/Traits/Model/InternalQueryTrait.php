<?php

namespace DS\Traits\Model;

use Phalcon\Mvc\Model\Criteria;
use Phalcon\Mvc\Model\Resultset\Simple;
use Phalcon\Mvc\ModelInterface;

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
trait InternalQueryTrait
{
    /**
     * @var Criteria
     */
    private $query;
    
    /**
     * @return Criteria
     */
    public function getQuery(): Criteria
    {
        return $this->query;
    }
    
    /**
     * @return array
     */
    public function executeQuery(): Simple
    {
        return $this->query->execute();
        
        //return $this->query ? $this->query->execute()->toArray() : [];
    }
    
    /**
     * @return ModelInterface|null
     */
    public function executeQueryFirst()
    {
        return $this->query ? $this->query->execute()->getFirst() : null;
    }
    
    /**
     * @param int $limit
     * @param int $offset
     *
     * @return $this
     */
    public function setLimit(int $limit, int $offset = 0): self
    {
        $this->query->limit($limit, $offset);
        
        return $this;
    }
    
    /**
     * @param string $field
     * @param string $ascDesc
     *
     * @return self
     */
    protected function orderBy(string $field, string $ascDesc = 'DESC'): self
    {
        if ($this->query)
        {
            $this->query
                ->orderBy(self::class . '.' . $field . ' ' . $ascDesc);
        }
        
        return $this;
    }
}
