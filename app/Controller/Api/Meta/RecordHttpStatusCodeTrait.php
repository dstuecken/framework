<?php

namespace DS\Controller\Api\Meta;

trait RecordHttpStatusCodeTrait
{
    protected $httpStatusCode = 200;
    
    /**
     * @return int
     */
    public function getHTTPStatusCode(): int
    {
        return $this->httpStatusCode;
    }
    
    /**
     * @param int $code
     *
     * @return $this
     */
    public function setHTTPStatusCode(int $code)
    {
        $this->httpStatusCode = $code;
        
        return $this;
    }
}
