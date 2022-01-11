<?php

namespace DS\Component\Filesystem\Flysystem;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;

/**
 * DS-Framework
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component\Filesystem
 */
class AwsS3FlysystemAdapter extends AwsS3Adapter implements DsFlysystemAdapterInterface
{
    use WebPathTrait, DefaultConfigTrait;

    /**
     * @var S3Client
     */
    public static $client = null;

    /**
     * @param array $config
     *
     * @return AwsS3FlysystemAdapter
     */
    public static function instance(array $config): AwsS3FlysystemAdapter
    {
        AwsS3FlysystemAdapter::$client  = new S3Client($config);
        AwsS3FlysystemAdapter::$webPath = $config['webpath'];

        return new AwsS3FlysystemAdapter(AwsS3FlysystemAdapter::$client, $config['bucket']);
    }
}
