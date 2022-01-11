<?php
namespace DS\Controller\Api\Meta;

use DS\Model\Base;

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
final class Record implements \Countable, \JsonSerializable, RecordInterface
{
    /**
     * @var mixed
     */
    private $data;

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->data;
    }

    /**
     * @return int|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param int|null $records
     *
     * @return $this
     */
    public function setRecord($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Records constructor.
     *
     * @param mixed $records
     */
    public function __construct($record = null)
    {
        if ($record instanceof Base)
        {
            $record = $record->toArray();
        }

        $this->data = $record;
    }

    /**
     * Count elements of an object
     *
     * @link  http://php.net/manual/en/countable.count.php
     * @return int The custom count as an integer.
     *        </p>
     *        <p>
     *        The return value is cast to an integer.
     * @since 5.1.0
     */
    public function count()
    {
        return $this->data ? 1 : 0;
    }
}
