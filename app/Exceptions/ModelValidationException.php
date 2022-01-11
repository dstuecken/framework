<?php

namespace DS\Exceptions;

use DS\Model\Base;
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
class ModelValidationException extends ModelException
{

    /**
     * @var string
     */
    protected $field = '';

    public function getField()
    {
        return $this->field;
    }

    /**
     * ModelFieldNotNullException constructor.
     *
     * @param Base           $model
     * @param string         $message
     * @param int            $code
     * @param Throwable|null $previous
     */
    public function __construct(Base $model, string $message = "", string $field = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($model, $message, $code, $previous);

        $this->field = $field;
    }
}
