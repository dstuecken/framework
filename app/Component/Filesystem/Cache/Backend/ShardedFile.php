<?php
namespace DS\Component\Filesystem\Cache\Backend;

use Phalcon\Cache\Backend\File;

/**
 * DS-Framework
 *
 * @author Dennis Stücken
 * @license proprietary

 * @copyright Dennis Stücken
 * @link https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component\Filesystem\GithubCache
 */
class ShardedFile extends File
{

    /**
     * @param string $keyName
     */
    private function shard($keyName)
    {
        if (strlen($keyName) > 4)
        {
            $shardedPath = $this->_options['cacheDirRoot'] .
                $keyName[0] . DIRECTORY_SEPARATOR .
                $keyName[1] . DIRECTORY_SEPARATOR .
                $keyName[2] . DIRECTORY_SEPARATOR .
                $keyName[3] . DIRECTORY_SEPARATOR;

            if (!file_exists($shardedPath))
            {
                mkdir($shardedPath, 0777, true);
            }

            $this->_options['cacheDir'] = $shardedPath;
        }
    }

    /**
     * Deletes a value from the cache by its key
     *
     * @param int|string $keyName
     *
     * @return boolean
     */
    public function delete($keyName)
    {
        $this->shard($keyName);

        parent::delete($keyName);
    }

    /**
     * Increment of a given key, by number $value
     *
     * @param string|int $keyName
     * @param int        $value
     *
     * @return mixed
     */
    public function increment($keyName = null, $value = 1)
    {
        $this->shard($keyName);

        parent::increment($keyName, $value);
    }

    /**
     * Decrement of a given key, by number $value
     *
     * @param string|int $keyName
     * @param int        $value
     *
     * @return mixed
     */
    public function decrement($keyName = null, $value = 1)
    {
        $this->shard($keyName);

        parent::decrement($keyName, $value);
    }

    /**
     * Checks if cache exists and it isn't expired
     *
     * @param string|int $keyName
     * @param int        $lifetime
     *
     * @return boolean
     */
    public function exists($keyName = null, $lifetime = null)
    {
        $this->shard($keyName);

        return parent::exists($keyName, $lifetime);

    }

    /**
     * Returns a cached content
     *
     * @param int|string $keyName
     * @param int        $lifetime
     *
     * @return mixed
     */
    public function get($keyName, $lifetime = null)
    {
        $this->shard($keyName);

        //var_dump($this->_options['cacheDir']);
        //var_dump($keyName . ": " . parent::get($keyName, $lifetime));

        return parent::get($keyName, $lifetime);
    }

    /**
     * Stores cached content into the file backend and stops the frontend
     *
     * @param int|string $keyName
     * @param string     $content
     * @param int        $lifetime
     * @param boolean    $stopBuffer
     */
    public function save($keyName = null, $content = null, $lifetime = null, $stopBuffer = true)
    {
        $this->shard($keyName);

        parent::save($keyName, $content, $lifetime, $stopBuffer);
    }

    /**
     * ShardedFilesystemCache constructor.
     *
     * @param \Phalcon\Cache\FrontendInterface $frontend
     * @param mixed|null                       $options
     */
    public function __construct($frontend, $options)
    {
        parent::__construct($frontend, $options);

        $this->_options['cacheDirRoot'] = $this->_options['cacheDir'];
    }

}
