<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 12.06.16
 * Time: 22:58
 */

namespace Oxa\VideoBundle\Twig\Extension;

use Oxa\VideoBundle\Entity\VideoMedia;

class VideoMediaThumbnailExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return ['render_video_thumbnail' => new \Twig_Function_Method($this, 'renderVideoThumbnail')];
    }

    public function renderVideoThumbnail(VideoMedia $media, array $dimensions = [])
    {
        $url = $media->getThumbnail()->getUrl();

        if (!empty($dimensions) && isset($dimensions['width']) && isset($dimensions['height'])) {
            if (strstr($url, 'image_crop_resized=200x120')) {
                $url = str_replace(
                    'image_crop_resized=200x120',
                    'image_crop_resized=' . $dimensions['width'] . 'x' . $dimensions['height'],
                    $url
                );
            }
        }

        return '<img src="' . $url . '">';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'video_media_thumbnail_extension';
    }
}
