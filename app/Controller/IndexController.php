<?php

namespace DS\Controller;

use DS\Exceptions\RuntimeException;

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
     *
     * @throws RuntimeException
     */
    public function indexAction()
    {
        throw new RuntimeException('Override the IndexController for the index route "/".');
    }
}
