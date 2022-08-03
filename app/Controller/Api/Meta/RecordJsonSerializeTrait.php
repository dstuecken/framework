<?php

namespace DS\Controller\Api\Meta;

trait RecordJsonSerializeTrait
{
    /**
     * @return string
     * @throws \JsonException
     */
    public function jsonSerialize(): string
    {
        return json_encode($this->data, JSON_THROW_ON_ERROR);
    }
}
