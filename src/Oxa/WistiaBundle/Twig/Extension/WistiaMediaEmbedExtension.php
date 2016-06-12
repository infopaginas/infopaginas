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

class WistiaMediaEmbedExtension extends \Twig_Extension
{
    private $wistiaEmbedAPIManager;

    public function __construct(WistiaEmbedAPIManager $embedAPIManager)
    {
        $this->wistiaEmbedAPIManager = $embedAPIManager;
    }

    public function getFunctions()
    {
        return ['render_wistia_embed' => new \Twig_Function_Method($this, 'renderWistiaEmbed')];
    }

    public function renderWistiaEmbed(WistiaMedia $media, array $dimensions = []) : string
    {
        $embedData = $this->wistiaEmbedAPIManager->get($media->getHashedId(), $dimensions);
        return $embedData['html'] ?? '';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'wistia_media_embed_extension';
    }
}