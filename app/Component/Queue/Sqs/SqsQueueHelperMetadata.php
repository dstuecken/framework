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
class SqsQueueHelperMetadata extends SqsQueueHelper
{
    /**
     * @return string
     */
    public static function getQueueUrl(): string
    {
        return 'https://sqs.us-west-2.amazonaws.com/867387728632/CollectionMetadataScan-' . ENV . '.fifo';
    }
    
    /**
     * @param int    $tokenId
     * @param int    $collectionId
     * @param string $contractAddress
     * @param string $tokenUrl
     * @param bool   $callTokenURIFromSmartContract
     * @param        $tokenIndex
     *
     * @return array
     */
    public static function getSqsMessage(int $tokenId, int $collectionId, string $contractAddress, string $tokenUrl, bool $callTokenURIFromSmartContract, $tokenIndex): array
    {
        return [
            'Id' => $collectionId . '-' . $tokenId,
            //'MessageDeduplicationId' => $collectionId . '-' . $tokenId,
            'MessageGroupId' => $collectionId . '-' . $tokenId,
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
                    'tokenId' => [
                        'DataType' => "Number",
                        'StringValue' => (string) $tokenId,
                    ],
                    'tokenIndex' => [
                        'DataType' => "Number",
                        'StringValue' => (string) $tokenIndex,
                    ],
                    'callTokenURI' => [
                        'DataType' => "Number",
                        'StringValue' => $callTokenURIFromSmartContract ? "1" : "0",
                    ],
                ],
            'MessageBody' => $tokenUrl,
        ];
    }
}
