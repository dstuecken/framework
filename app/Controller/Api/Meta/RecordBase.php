<?php
namespace DS\Controller\Api\Meta;

abstract class RecordBase {
    /**
     * Define _meta data
     * @var array
     */
    protected $meta = [];

    public function getMeta(): array
    {
        return $this->meta;
    }

    public function setMeta(array $meta): void
    {
        $this->meta = $meta;
    }
}