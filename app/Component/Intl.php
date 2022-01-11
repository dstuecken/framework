<?php
namespace DS\Component;

use DS\Component\Intl\NativeArray;
use Phalcon\Di\AbstractInjectionAware;
use Phalcon\Http\Request;

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
class Intl extends AbstractInjectionAware
{

    /**
     *
     */
    public function init()
    {
        /**
         * @var $request Request
         */
        $request = $this->getDI()->get('request');
        $messages = [];

        if ($request)
        {
            // Ask browser what is the best language
            $language = explode('-', $request->getBestLanguage())[0];

            // Check if we have a translation file for that lang
            if ($language && file_exists(APP_PATH . "Messages/" . $language . ".php"))
            {
                /** @noinspection PhpIncludeInspection */
                $messages = require APP_PATH . "Messages/" . $language . ".php";
            }
            else
            {
                // Fallback to some default
                $messages = require APP_PATH . "Messages/en.php";
            }
        }

        // Return a translation object
        return new NativeArray(
            array(
                "content" => $messages
            )
        );
    }
}
