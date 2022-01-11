<?php

namespace DS\Component\Mail\ViewParams;

use DS\Application;

/**
 * DS-Framework
 *
 * Mailing
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component\Mail
 */
class DefaultParams
{
    /**
     * @var string
     */
    public $headerMessage = '';

    /**
     * @var bool
     */
    public $showUnsubscribeLink = true;

    /**
     * @var bool
     */
    public $showNotificationUnsubscribeLink = true;

    /**
     * @var string
     */
    public $topMessage = '';

    /**
     * @var string
     */
    public $bottomMessage = '';

    /**
     * @var string
     */
    public $buttonText = '';

    /**
     * @var string
     */
    public $buttonLink = '';

    /**
     * @var string
     */
    public $companyAddress = '';

    /**
     * @var string
     */
    public $url = '';

    /**
     * @var string
     */
    public $imageUrl = '';

    /**
     * @var bool
     */
    public $buttonCentered = false;

    /**
     * @return array
     */
    public function toArray()
    {
        return get_object_vars($this);
    }

    /**
     * DefaultParams constructor.
     *
     * @throws \Phalcon\Exception
     */
    public function __construct()
    {
        $config = application()->getConfig();

        $this->url = $config['url'];
    }
}
