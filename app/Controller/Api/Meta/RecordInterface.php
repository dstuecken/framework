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
     * @return array
     */
    public function getData();

    /**
     * @return string
     */
    public function jsonSerialize();

    /**
     * @return int
     */
    public function count();
}
