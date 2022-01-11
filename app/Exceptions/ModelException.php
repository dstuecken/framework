<?php

namespace DS\Exceptions;

use DS\Model\Base;
use Phalcon\Exception;
use Throwable;

/**
 * DS-Framework
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Controller
 */
class ModelException extends Exception
{
    /**
     * @var Base
     */
    protected $model;
    
    /**
     * @return Base
     */
    public function getModel(): Base
    {
        return $this->model;
    }
    
    /**
     * ModelFieldNotNullException constructor.
     *
     * @param Base           $model
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(Base $model, string $message = "", int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        
        $this->model = $model;
    }
}
