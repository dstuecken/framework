<?php

namespace DS\Component\Http;

/**
 * DS-Framework
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component
 */
class HttpRequestBatch
{
    /**
     * @var string
     */
    public $url;
    
    /**
     * @var callable
     */
    public $callback;
    
    /**
     * @var callable
     */
    public $rejectedCallback;
}
