<?php

namespace Oxa\VideoBundle\Twig\Extension;

use Oxa\VideoBundle\Entity\VideoMedia;
use Oxa\VideoBundle\Manager\VideoEmbedAPIManager;
use Oxa\VideoBundle\Manager\VideoMediaEmbedManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class VideoMediaEmbedExtension
 * @package Oxa\VideoBundle\Twig\Extension
 */
class VideoMediaEmbedExtension extends \Twig_Extension
{
    /**
     * @var VideoMediaEmbedManager
     */
    private $videoEmbedManager;

    /**
     * VideoMediaEmbedExtension constructor.
     * @param VideoMediaEmbedManager $videoMediaEmbedManager
     */
    public function __construct(
            VideoMediaEmbedManager $videoMediaEmbedManager,
            ContainerInterface $container
    ) {
        $this->videoEmbedManager = $videoMediaEmbedManager;
        $this->container = $container;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return ['render_video_embed' => new \Twig_Function_Method($this, 'renderVideoEmbed')];
    }

    /**
     * @param VideoMedia $media
     * @param array $dimensions
     * @return string
     */
    public function renderVideoEmbed(VideoMedia $media, array $dimensions = []) : string
    {
        if (empty($dimensions['height'])) {
            $dimensions['height'] = 400;
        }
        if (empty($dimensions['width'])) {
            $dimensions['width'] = 600;
        }
        $html = $this->container->get('templating')
                ->render(
                    "Oxa_Video:VideoMedia/video_embed.html.twig", [
                        'media'     => $media,
                        'dimesions' => $dimensions,
                    ]
                );
        return $html;
        $html = $this->videoEmbedManager->getHTML($media, $dimensions);
        return $html;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'video_media_embed_extension';
    }
}
