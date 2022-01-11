<?php
namespace DS\Controller\Api\Meta;

/**
 *
 * DS-Framework
 *
 * @author Dennis Stücken
 * @license proprietary

 * @copyright Dennis Stücken
 * @link https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Controller
 */
final class MetaObject implements \JsonSerializable
{
    /**
     * @var int|null
     */
    public $total = null;

    /**
     * @var bool
     */
    public $status;

    /**
     * @var bool
     */
    public $success = true;

    /**
     * @return array
     */
    function jsonSerialize()
    {
        return [
            'total' => $this->total,
            // 'status' => $this->status,
            'success' => $this->success,
        ];
    }

    /**
     * Response constructor.
     *
     */
    public function __construct($status, $total = 0, $success = true)
    {
        $this->status  = $status;
        $this->total   = $total;
        $this->success = $success;
    }
}
