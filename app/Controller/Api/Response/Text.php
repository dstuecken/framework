<?php
namespace DS\Controller\Api\Response;

use DS\Controller\Api\Meta\RecordInterface;
use DS\Controller\Api\Response;

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
class Text extends Response
{

    /**
     * Set content type to plain text
     */
    protected function setResponseType()
    {
        $this->response->setContentType('text/plain');
    }

    /**
     * @param mixed $content
     *
     * @return $this
     */
    public function set(RecordInterface $content)
    {
        $this->response->setContent($content);
        $this->response->setStatusCode($content->getHTTPStatusCode());
        
        return $this;
    }
}
