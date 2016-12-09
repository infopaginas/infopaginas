<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 12.06.16
 * Time: 22:58
 */

namespace Oxa\VideoBundle\Twig\Extension;

use Oxa\VideoBundle\Entity\VideoMedia;
use Oxa\VideoBundle\Manager\VideoEmbedAPIManager;
use Oxa\VideoBundle\Manager\VideoMediaEmbedManager;

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
    public function __construct(VideoMediaEmbedManager $videoMediaEmbedManager)
    {
        $this->videoEmbedManager = $videoMediaEmbedManager;
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
