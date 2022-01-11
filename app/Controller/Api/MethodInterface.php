<?php
namespace DS\Controller\Api;

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
interface MethodInterface
{
    /**
     * Process api request
     *
     * Return value is directly sent via Response
     *
     * @return mixed
     */
    public function process();

    /**
     * Return valid md5 hashed etag for this api method
     *
     * @return mixed
     */
    public function getEtag();

}
