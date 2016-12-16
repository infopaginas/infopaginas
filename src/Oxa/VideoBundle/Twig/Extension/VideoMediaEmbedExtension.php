<?php

namespace Oxa\VideoBundle\Twig\Extension;

use Gaufrette\Filesystem;
use Oxa\VideoBundle\Entity\VideoMedia;
use Oxa\VideoBundle\Manager\VideoEmbedAPIManager;
use Oxa\VideoBundle\Manager\VideoMediaEmbedManager;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class VideoMediaEmbedExtension
 * @package Oxa\VideoBundle\Twig\Extension
 */
class VideoMediaEmbedExtension extends \Twig_Extension
{
    const DEFAULT_VIDEO_WIDTH  = 640;

    const DEFAULT_VIDEO_HEIGHT = 480;

    /**
     * @var VideoMediaEmbedManager
     */
    private $templating;
    private $filesystem;

    /**
     * VideoMediaEmbedExtension constructor.
     * @param VideoMediaEmbedManager $videoMediaEmbedManager
     */
    public function __construct(EngineInterface $templating, Filesystem $filesystem) {
        $this->templating        = $templating;
        $this->filesystem        = $filesystem;
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
            $dimensions['height'] = self::DEFAULT_VIDEO_HEIGHT;
        }
        if (empty($dimensions['width'])) {
            $dimensions['width'] = self::DEFAULT_VIDEO_WIDTH;
        }

        $expires = new \DateTime();
        $expires->modify('+ 600 seconds');


        $url = $this->filesystem->getAdapter()->getUrl($media->getFilepath().$media->getFilename(),['expires' => $expires->getTimestamp()]);
        $html = $this->templating
                ->render(
                    ":redesign/blocks/video:video_embed.html.twig", [
                        'media'         => $media,
                        'dimensions'    => $dimensions,
                        'url'           => $url,
                    ]
                );
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
