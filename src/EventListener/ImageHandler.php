<?php
/**
 * Created by PhpStorm.
 * Member: mimosa
 * Date: 20.12.19
 * Time: 13:36
 */

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Blog;
use App\Entity\Expression;
use App\Entity\Joke;
use App\Entity\Proverb;
use App\Entity\Word;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Vich\UploaderBundle\Event\Event;
use Vich\UploaderBundle\Event\Events;
use Symfony\Component\HttpFoundation\File\File;
use Intervention\Image\ImageManagerStatic;

/**
 * Class ImageHandler
 * @package App\EventListener
 */
class ImageHandler implements EventSubscriberInterface
{
    /**
     * @param Event $event
     */
    public function resizeImage(Event $event): void
    {
        /** @var Word|Expression|Proverb|Joke|Blog $post */
        $post = $event->getObject();

        /** @var File $imageUpload */
        $imageUpload = $post->getImageFile();
        $imageInfo = \getimagesize($imageUpload->getRealPath());

        if (!empty($imageInfo)) {
            $width = $imageInfo[0];
            $height = $imageInfo[1];

            $post
                ->setImageWidth($width)
                ->setImageHeight($height);

            if ($width > 640) {
                $newImage = ImageManagerStatic::make($imageUpload->getRealPath())
                    ->resize(640, 480);

                $newImage->save($newImage->dirname . DIRECTORY_SEPARATOR . $newImage->basename, 75);
            }
        } else {
            error_log('Image with id = ' . $post->getId() . ' has no image info');
        }
    }

    /**
     * @inheritDoc
     */
    public static function getSubscribedEvents()
    {
        return [
            Events::POST_UPLOAD => 'resizeImage'
        ];
    }
}