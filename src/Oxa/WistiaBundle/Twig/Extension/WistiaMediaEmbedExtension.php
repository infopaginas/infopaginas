<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 12.06.16
 * Time: 22:58
 */

namespace Oxa\WistiaBundle\Twig\Extension;


use Oxa\WistiaBundle\Entity\WistiaMedia;
use Oxa\WistiaBundle\Manager\WistiaEmbedAPIManager;
use Oxa\WistiaBundle\Manager\WistiaMediaEmbedManager;

/**
 * Class WistiaMediaEmbedExtension
 * @package Oxa\WistiaBundle\Twig\Extension
 */
class WistiaMediaEmbedExtension extends \Twig_Extension
{
    /**
     * @var WistiaMediaEmbedManager
     */
    private $wistiaEmbedManager;

    /**
     * WistiaMediaEmbedExtension constructor.
     * @param WistiaMediaEmbedManager $wistiaMediaEmbedManager
     */
    public function __construct(WistiaMediaEmbedManager $wistiaMediaEmbedManager)
    {
        $this->wistiaEmbedManager = $wistiaMediaEmbedManager;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return ['render_wistia_embed' => new \Twig_Function_Method($this, 'renderWistiaEmbed')];
    }

    /**
     * @param WistiaMedia $media
     * @param array $dimensions
     * @return string
     */
    public function renderWistiaEmbed(WistiaMedia $media, array $dimensions = []) : string
    {
        $html = $this->wistiaEmbedManager->getHTML($media, $dimensions);
        return $html;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'wistia_media_embed_extension';
    }
}