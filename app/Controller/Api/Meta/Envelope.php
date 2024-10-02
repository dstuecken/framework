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
    public function jsonSerialize()
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
     * @param bool $success
     */
    public function __construct(?RecordInterface $records = null, ?bool $success = true)
    {
        if ($records !== null)
        {
            $this->_meta = new MetaObject(
                $records->count(),
                $success,
                $records->getMeta(),
            );
        }
        else
        {
            $this->_meta = new MetaObject(
                null,
                $success,
                $records->getMeta(),
            );
        }

        if ($records)
        {
            $this->data = $records->getData();
        }
    }
}
