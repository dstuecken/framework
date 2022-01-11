<?php

namespace DS\Component\Queue\Sqs;

/**
 * DS-Framework
 *
 * Queueing
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component\Mail
 */
class SqsQueueHelperOpenseaUpdate extends SqsQueueHelper
{
    /**
     * @return string
     */
    public static function getQueueUrl(): string
    {
        return 'https://sqs.us-west-2.amazonaws.com/867387728632/OpenSeaPriceUpdate-production';
    }
    
    /**
     * @param array  $tokenIds
     * @param int    $collectionId
     * @param string $contractAddress
     *
     * @return array
     */
    public static function getSqsMessage(array $tokenIds, int $collectionId, string $contractAddress): array
    {
        return [
            'Id' => uniqid($collectionId, true),
            'MessageAttributes' =>
                [
                    'collectionId' => [
                        'DataType' => "Number",
                        'StringValue' => (string) $collectionId,
                    ],
                    'address' => [
                        'DataType' => "String",
                        'StringValue' => $contractAddress,
                    ],
                    'tokenIds' => [
                        'DataType' => "String",
                        'StringValue' => implode(',', $tokenIds),
                    ],
                ],
            'MessageBody' => '',
        ];
    }
}
