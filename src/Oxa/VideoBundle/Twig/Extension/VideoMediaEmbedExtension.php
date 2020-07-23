<?php

namespace Oxa\VideoBundle\Twig\Extension;

use Oxa\VideoBundle\Entity\VideoMedia;
use Oxa\VideoBundle\Manager\VideoManager;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class VideoMediaEmbedExtension
 * @package Oxa\VideoBundle\Twig\Extension
 */
class VideoMediaEmbedExtension extends AbstractExtension
{
    const DEFAULT_VIDEO_WIDTH  = 640;
    const DEFAULT_VIDEO_HEIGHT = 480;

    private $videoManager;

    /**
     * @param VideoManager $videoManager
     */
    public function __construct(VideoManager $videoManager)
    {
        $this->videoManager = $videoManager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction(
                'render_video_embed',
                [$this, 'renderVideoEmbed'],
                [
                    'needs_environment' => true,
                    'is_safe' => [
                        'html',
                        'js',
                    ],
                ]
            ),
            new TwigFunction(
                'render_video_admin_embed',
                [$this, 'renderAdminVideoPreview'],
                [
                    'needs_environment' => true,
                    'is_safe' => [
                        'html',
                        'js',
                    ],
                ]
            ),
        ];
    }

    /**
     * @param Environment $env
     * @param VideoMedia        $media
     * @param array             $dimensions
     *
     * @return string
     */
    public function renderVideoEmbed(Environment $env, VideoMedia $media, array $dimensions = [])
    {
        if (empty($dimensions['height'])) {
            $dimensions['height'] = self::DEFAULT_VIDEO_HEIGHT;
        }
        if (empty($dimensions['width'])) {
            $dimensions['width'] = self::DEFAULT_VIDEO_WIDTH;
        }

        $url = $this->videoManager->getPublicUrl($media);

        $html = $env->render(
            ':redesign/blocks/video:video_embed.html.twig',
            [
                'media'         => $media,
                'dimensions'    => $dimensions,
                'url'           => $url,
            ]
        );

        return $html;
    }

    /**
     * @param Environment $env
     * @param VideoMedia        $media
     *
     * @return string
     */
    public function renderAdminVideoPreview(Environment $env, VideoMedia $media)
    {
        $url = $this->videoManager->getPublicUrl($media);

        $html = $env->render(
            ':redesign/blocks/video:video_admin_embed.html.twig',
            [
                'media'         => $media,
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
