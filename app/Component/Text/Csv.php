<?php

namespace DS\Component\Text;

use League\Csv\Writer;

/**
 * DS-Framework
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component\Text
 */
class Csv
{
    /**
     * @param array $data
     *
     * @throws \Exception
     * @throws \League\Csv\CannotInsertRecord
     */
    public function writeFromArray(array $data, callable $cellFormatter, $headers = [])
    {
        $csvWriter = Writer::createFromString('');
        if (count($data))
        {
            if (count($headers))
            {
                $csvWriter->insertOne($headers);
            }

            foreach ($data as $row)
            {
                $csvWriter->insertOne($cellFormatter($row));
            }

        }

        return $csvWriter;
    }

    /**
     * Parse multiline csv text string to array
     *
     * @param string $csvString
     * @param string $delimiter
     * @param bool   $skipEmptyLines
     * @param bool   $trimFields
     *
     * @return \Iterator
     */
    public function parseFromText(string $csvString, string $delimiter = ",", $skipEmptyLines = true, $trimFields = false)
    {
        if (!ini_get("auto_detect_line_endings"))
        {
            ini_set("auto_detect_line_endings", '1');
        }

        //$reader = Reader::createFromString($csvString);
        //$reader->setDelimiter($delimiter);

        //return $reader->getRecords();

        $enc   = preg_replace('/(?<!")""/', '!!Q!!', $csvString);
        $enc   = preg_replace_callback(
            '/"(.*?)"/s',
            function ($field) {
                return urlencode($field[1]);
            },
            $enc
        );
        $lines = preg_split($skipEmptyLines ? ($trimFields ? '/( *\R)+/s' : '/\R+/s') : '/\R/s', $enc);

        return array_map(
            function ($line) use ($delimiter, $trimFields) {
                $fields = $trimFields ? array_map('trim', explode($delimiter, $line)) : explode($delimiter, $line);

                return array_map(
                    function ($field) {
                        return str_replace('!!Q!!', '"', urldecode($field));
                    },
                    $fields
                );
            },
            $lines
        );
    }

}
