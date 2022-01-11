<?php

namespace DS\Component\Queue\Sqs;

use Aws\Sqs\SqsClient;

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
abstract class SqsQueueHelper
{
    /**
     * @return string
     */
    abstract public static function getQueueUrl(): string;
    
    /**
     * @var array
     */
    protected static $batch = [];
    
    /**
     * @param array $message
     *
     * @return bool
     */
    public static function addBatch(array $message): bool
    {
        static::$batch[] = $message;
        
        return false;
    }
    
    /**
     * @param SqsClient $sqsClient
     * @param int       $threshold Send every X batches, needs to be bigger than 0
     *
     * @return bool
     */
    public static function sendSqsBatch(SqsClient $sqsClient, int $threshold = 10): bool
    {
        $count = count(static::$batch);
        if ($count >= $threshold)
        {
            //echo "{$this->getName()}: Sending last SQS batch - " . count($messageBatch) . "\n";
            $sqsClient->sendMessageBatch(
                [
                    'Entries' => static::$batch,
                    'QueueUrl' => static::getQueueUrl(),
                ]
            );
            
            // clear batch
            static::$batch = [];
            
            return true;
        }
        
        return false;
    }
}
