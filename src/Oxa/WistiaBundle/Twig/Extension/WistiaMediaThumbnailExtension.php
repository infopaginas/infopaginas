<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 12.06.16
 * Time: 22:58
 */

namespace Oxa\WistiaBundle\Twig\Extension;

use Oxa\WistiaBundle\Entity\WistiaMedia;

class WistiaMediaThumbnailExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return ['render_wistia_thumbnail' => new \Twig_Function_Method($this, 'renderWistiaThumbnail')];
    }

    public function renderWistiaThumbnail(WistiaMedia $media, array $dimensions = [])
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
        return 'wistia_media_thumbnail_extension';
    }
}
