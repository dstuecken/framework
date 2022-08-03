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
interface RecordInterface
{
    /**
     * @return array|mixed
     */
    public function getData();

    /**
     * @return string
     */
    public function jsonSerialize(): string;

    /**
     * @return int
     */
    public function count(): int;
    
    /**
     * @return int
     */
    public function getHTTPStatusCode(): int;
}
