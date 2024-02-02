<?php

namespace DS\Controller\Api\Meta;

use ArrayAccess;
use Countable;
use JsonSerializable;

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
final class Records extends RecordBase implements ArrayAccess, Countable, JsonSerializable, RecordInterface
{
    use RecordHttpStatusCodeTrait, RecordJsonSerializeTrait;

    /**
     * The records
     *
     * @var array|mixed
     */
    private $data;

    /**
     * Total records. In case it is only a portion.
     *
     * @var int
     */
    private $totals;

    /**
     * @return array
     */
    public function toArray()
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
     * @param int|null $data
     *
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * @return int
     */
    public function getTotals()
    {
        return $this->totals;
    }

    /**
     * @param int $totals
     *
     * @return $this
     */
    public function setTotals($totals)
    {
        $this->totals = $totals;

        return $this;
    }

    /**
     * Records constructor.
     *
     * @param mixed $records
     */
    public function __construct(array $records, ?int $totals = null, array $meta = [])
    {
        $this->data = $records;

        if ($totals === null)
        {
            $this->totals = count($records);
        }
        else
        {
            $this->totals = $totals;
        }

        $this->meta = $meta;
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
    public function count(): int
    {
        return $this->totals;
    }

    /**
     * Whether a offset exists
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @param mixed $offset <p>
     *                      An offset to check for.
     *                      </p>
     *
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * Offset to retrieve
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetget.php
     *
     * @param mixed $offset <p>
     *                      The offset to retrieve.
     *                      </p>
     *
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset];
    }

    /**
     * Offset to set
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetset.php
     *
     * @param mixed $offset <p>
     *                      The offset to assign the value to.
     *                      </p>
     * @param mixed $value <p>
     *                      The value to set.
     *                      </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * Offset to unset
     *
     * @link  http://php.net/manual/en/arrayaccess.offsetunset.php
     *
     * @param mixed $offset <p>
     *                      The offset to unset.
     *                      </p>
     *
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

}
