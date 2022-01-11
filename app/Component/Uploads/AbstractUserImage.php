<?php

namespace DS\Component\Uploads;

/**
 * Dennis Stücken
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component
 */
abstract class AbstractUserImage extends AbstractImage
{
    /**
     * @var string
     */
    protected $imageDir = 'user-images/';

    /**
     * @var string
     */
    protected $imagePrefix = 'userImage-';
}
