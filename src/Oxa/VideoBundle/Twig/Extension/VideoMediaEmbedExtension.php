<?php

namespace Oxa\VideoBundle\Twig\Extension;

use Gaufrette\Filesystem;
use Oxa\VideoBundle\Entity\VideoMedia;

/**
 * Class VideoMediaEmbedExtension
 * @package Oxa\VideoBundle\Twig\Extension
 */
class VideoMediaEmbedExtension extends \Twig_Extension
{
    const DEFAULT_VIDEO_WIDTH  = 640;
    const DEFAULT_VIDEO_HEIGHT = 480;

    private $filesystem;

    /**
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            'render_video_embed' => new \Twig_Function_Method(
                $this,
                'renderVideoEmbed',
                [
                    'needs_environment' => true,
                    'is_safe' => [
                        'html',
                        'js',
                    ],
                ]
            ),
            'render_video_admin_embed' => new \Twig_Function_Method(
                $this,
                'renderAdminVideoPreview',
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
     * @param \Twig_Environment $env
     * @param VideoMedia        $media
     * @param array             $dimensions
     *
     * @return string
     */
    public function renderVideoEmbed(\Twig_Environment $env, VideoMedia $media, array $dimensions = [])
    {
        if (empty($dimensions['height'])) {
            $dimensions['height'] = self::DEFAULT_VIDEO_HEIGHT;
        }
        if (empty($dimensions['width'])) {
            $dimensions['width'] = self::DEFAULT_VIDEO_WIDTH;
        }

        $expires = new \DateTime();
        $expires->modify('+ 600 seconds');

        $url = $this->filesystem->getAdapter()->getUrl(
            $media->getFilepath() . $media->getFilename(),
            [
                'expires' => $expires->getTimestamp(),
            ]
        );

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
     * @param \Twig_Environment $env
     * @param VideoMedia        $media
     *
     * @return string
     */
    public function renderAdminVideoPreview(\Twig_Environment $env, VideoMedia $media)
    {
        $url = $this->filesystem->getAdapter()->getUrl($media->getFilepath() . $media->getFilename());

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
