<?php

namespace DS\ViewModel;

use DS\Component\DiInjection;
use Phalcon\Di\InjectionAwareInterface;

/**
 * DS-Framework
 *
 * Base viewmodel
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Model
 */
abstract class Base
    extends DiInjection
    implements InjectionAwareInterface
{
}
