<?php
namespace DS\Component;

use Phalcon\Di;

/**
 * DS-Framework
 *
 * @author Dennis Stücken
 * @license proprietary

 * @copyright Dennis Stücken
 * @link https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component
 */
abstract class DiInjection
    implements Di\InjectionAwareInterface
{
    use \DS\Traits\DiInjection;
}
