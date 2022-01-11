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
 */
trait InternalQueryOrderTrait
{
    use InternalQueryTrait;
    
    /**
     * @param string $ascDesc
     *
     * @return $this
     */
    public function orderByName($ascDesc = 'ASC'): self
    {
        if ($this->query)
        {
            $this->query->orderBy(self::class . '.name ' . $ascDesc);
        }
        
        return $this;
    }
    
    /**
     * @param string $ascDesc
     *
     * @return $this
     */
    public function orderById($ascDesc = 'DESC'): self
    {
        if ($this->query)
        {
            $this->query->orderBy(self::class . '.id ' . $ascDesc);
        }
        
        return $this;
    }
}
