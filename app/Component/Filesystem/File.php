<?php
namespace DS\Component\Filesystem;

use DS\Exceptions\FileNotFoundException;
use Phalcon\Exception;

/**
 * DS-Framework
 *
 * @author Dennis Stücken
 * @license proprietary

 * @copyright Dennis Stücken
 * @link https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component\Filesystem
 */
class File
{

    /**
     * Reads the last line of a file
     *
     * @param string $filePath
     *
     * @return string
     */
    public static function readLastLine($filePath)
    {
        if (!file_exists($filePath))
        {
            throw new FileNotFoundException(sprintf("The file path %s was not found.", $filePath));
        }

        if (!is_readable($filePath))
        {
            throw new Exception(sprintf("The file %s is not readable.", $filePath));
        }

        return self::reverseReadUntil("\n", $filePath);
    }

    /**
     * Reverse read a file until $needle position is found
     *
     * @param string $needle
     * @param string $filePath
     *
     * @return string
     */
    public static function reverseReadUntil($needle, $filePath)
    {
        $line = '';

        $f      = fopen($filePath, 'r');
        $cursor = -1;

        fseek($f, $cursor, SEEK_END);
        $char = fgetc($f);

        /**
         * Trim trailing newline chars of the file
         */
        while ($char === "\n" || $char === "\r")
        {
            fseek($f, $cursor--, SEEK_END);
            $char = fgetc($f);
        }

        /**
         * Read until the start of file or first newline char
         */
        while ($char !== false && $char !== $needle)
        {
            /**
             * Prepend the new char
             */
            $line = $char . $line;
            fseek($f, $cursor--, SEEK_END);
            $char = fgetc($f);
        }

        return $line;
    }
}
