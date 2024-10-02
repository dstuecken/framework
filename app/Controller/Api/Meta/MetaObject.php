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
     * Meta data object
     *
     * @var array
     */
    public $meta = [];

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->meta;
    }

    /**
     * Response constructor.
     *
     */
    public function __construct(?int $total = null, ?bool $success = true, ?array $additionalData = null)
    {
        if ($total !== null)
        {
            $this->meta['total'] = $total;
        }

        if ($success !== null)
        {
            $this->meta['success'] = $success;
        }

        if ($additionalData !== null)
        {
            foreach ($additionalData as $key => $value)
            {
                $this->meta[$key] = $value;
            }
        }
    }
}
