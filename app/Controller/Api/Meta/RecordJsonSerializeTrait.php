<?php

namespace DS\Controller\Api\Meta;

trait RecordJsonSerializeTrait
{
    /**
     * @return mixed
     * @throws \JsonException
     */
    public function jsonSerialize()
    {
        return $this->data;
    }
}
