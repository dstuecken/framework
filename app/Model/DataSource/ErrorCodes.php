<?php
namespace DS\Model\DataSource;

use DS\Traits\Singleton;

/**
 *
 * firewards
 *
 * @author Dennis Stücken
 * @license proprietary

 * @copyright Dennis Stücken
 * @link https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Model\DataSource
 */
class ErrorCodes
{
    use Singleton;

    /**
     * Error codes
     */
    const GeneralException = 100;
    const SessionExpired = 110;
    const ApiError = 120;
    const InvalidParameter = 130;
    const UserValidation = 140;

    /**
     * @var array
     */
    private $codes = [
        self::SessionExpired => 'Session Expired',
        self::ApiError => 'Api Error',
        self::GeneralException => 'General Exception',
        self::InvalidParameter => 'Invalid Parameter',
    ];

    /**
     * @return array
     */
    public function getCodes()
    {
        return $this->codes;
    }
}
