<?php

namespace DS\Controller;

use DS\Model\Client;
use Phalcon\Exception;
use Phalcon\Logger;

/**
 * DS
 *
 * @copyright 2017 | Dennis Stücken
 *
 * @version   $Version$
 * @package   DS\Controller
 */
class IndexController
    extends BaseFrontendController
{
    /**
     * Home
     */
    public function indexAction()
    {
        try
        {
            die("Override the IndexController for the index route \"/\".");
        }
        catch (Exception $e)
        {
            application()->log($e->getMessage(), Logger::CRITICAL);
        }
    }
}
