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
final class Envelope implements \JsonSerializable
{
    /**
     * @var MetaObject
     */
    public $_meta;

    /**
     * @var RecordInterface
     */
    public $data = null;

    /**
     * Json serializer
     */
    function jsonSerialize()
    {
        return [
            '_meta' => $this->_meta,
            'data' => $this->data,
        ];
    }

    /**
     * Envelope constructor.
     *
     * @param RecordInterface $records
     * @param bool         $success
     */
    public function __construct(RecordInterface $records = null, bool $success = true)
    {
        $status = ($success) ? 'SUCCESS' : 'ERROR';
        if ($records !== null)
        {
            $count = $records->count();

            $this->_meta = new MetaObject(
                $status,
                $count,
                $success
            );
        }
        else
        {
            $this->_meta = new MetaObject(
                $status,
                0,
                $success
            );
        }

        if ($records)
        {
            $this->data = $records->getData();
        }

        /*
        if ($this->_meta->count === 0)
        {
            // This is required to make the response JSON return an empty JS object.  Without
            // this, the JSON return an empty array:  [] instead of {}
            $this->data = new \stdClass();
        }
        else
        {
            $this->data = $records;
        }
        */
    }
}
