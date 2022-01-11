<?php

namespace DS\Component\Uploads;

use DS\Component\Filesystem\Flysystem\DsFlysystemAdapterInterface;
use DS\Component\Images\Extensions;
use DS\Component\Images\ImageResize;
use DS\Component\Uploads\Exceptions\DimensionException;
use DS\Model\User;
use Gumlet\ImageResizeException;
use Phalcon\Http\Request\File;

/**
 * Dennis Stücken
 *
 * @author    Dennis Stücken
 * @license   proprietary
 * @copyright Dennis Stücken
 * @link      https://www.dvlpr.de
 *
 * @version   $Version$
 * @package   DS\Component
 */
class UserImageUpload extends AbstractUserImage
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var File[]
     */
    private $uploadedFilesArray = [];

    /**
     * @param string $imageUrl
     *
     * @return string
     * @throws DimensionException
     */
    public function execute(string $imageUrl): string
    {
        foreach ($this->uploadedFilesArray as $file)
        {
            if (file_exists($file->getTempName()))
            {
                try
                {
                    // Resize
                    $image = ImageResize::resize($file->getTempName());

                    if ($image->getSourceHeight() >= 50 && $image->getSourceWidth() >= 50)
                    {
                        // Write image to post
                        $this->user->setImage($this->imagePrefix . md5($this->user->getId()) . '.' . Extensions::extensionBySourceType($image->source_type))->save();

                        // Write to flysystem
                        if ($this->files->has($this->imageDir . $this->user->getImage()))
                        {
                            $this->files->delete($this->imageDir . $this->user->getImage());
                        }

                        $stream = fopen($file->getTempName(), 'r+');
                        $this->files->writeStream($this->imageDir . $this->user->getImage(), $stream, $this->files->getDefaultConfig());
                        fclose($stream);

                        return $this->user->getImage();
                    }
                    else
                    {
                        throw new DimensionException(
                            'Please note that your image was not attached since it only has a dimension of ' . $image->getSourceHeight() . 'x' . $image->getSourceWidth(
                            ) . '. Minimum dimension is 50x50.'
                        );
                    }
                }
                catch (ImageResizeException $e)
                {
                    application()->log('UserImageUpload::execute: ' . $e->getMessage());
                }
            }
        }

        return '';
    }

    /**
     * @param DsFlysystemAdapterInterface $files
     * @param User                        $user
     * @param array                       $uploadedFilesArray
     *
     * @return UserImageUpload
     */
    public static function factory(DsFlysystemAdapterInterface $files, User $user, array $uploadedFilesArray)
    {
        return new self($files, $user, $uploadedFilesArray);
    }

    /**
     * UserImageUpload constructor.
     *
     * @param DsFlysystemAdapterInterface $files
     * @param User                        $user
     * @param array                       $uploadedFilesArray
     */
    public function __construct(DsFlysystemAdapterInterface $files, User $user, array $uploadedFilesArray)
    {
        parent::__construct($files);

        $this->user               = $user;
        $this->uploadedFilesArray = $uploadedFilesArray;
    }

}
