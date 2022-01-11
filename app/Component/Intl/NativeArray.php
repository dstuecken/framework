<?php
namespace DS\Component\Intl;

use Phalcon\Translate\Adapter\NativeArray as NativeArrayAdapter;

/**
 * DS-Framework
 *
 * @author Dennis Stücken
 * @license proprietary

 * @copyright Dennis Stücken
 * @link https://www.dvlpr.de
 *
 * @version $Version$
 * @package DS\Component
 */
class NativeArray extends NativeArrayAdapter
{

    /**
     * @param $date
     *
     * @return bool|string
     */
    public function formatDate($date)
    {
        return date($this->t('Y-m-d'), strtotime($date));
    }

    /**
     * @param $date
     *
     * @return bool|string
     */
    public function formatDateTime($date)
    {
        return date($this->t('Y-m-d H:i'), strtotime($date));
    }

    /**
     * @param $date
     *
     * @return bool|string
     */
    public function formatDateTimeAndSeconds($date)
    {
        return date($this->t('Y-m-d H:i:s'), strtotime($date));
    }

}
