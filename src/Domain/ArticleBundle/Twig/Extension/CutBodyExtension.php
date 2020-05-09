<?php

namespace Domain\ArticleBundle\Twig\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class CutBodyExtension
 * @package Domain\ArticleBundle\Twig\Extension
 */
class CutBodyExtension extends AbstractExtension
{
    const PREVIEW_BODY_LENGTH = 250;
    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('cut_body_extension', [$this, 'cutBody']),
        ];
    }

    /**
     * @param string $body
     * @return string
     */
    public function cutBody(string $body)
    {
        $body = strip_tags(html_entity_decode($body, ENT_QUOTES | ENT_HTML5));

        if (mb_strlen($body) > self::PREVIEW_BODY_LENGTH) {
            $body = mb_substr($body, 0, self::PREVIEW_BODY_LENGTH) . '...';
        }

        return $body;
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return 'cut_body_extension';
    }
}
