<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 18.06.16
 * Time: 12:34
 */

namespace Oxa\VideoBundle\Manager;

use Doctrine\ORM\EntityManager;
use Oxa\VideoBundle\Entity\VideoMedia;
use Oxa\VideoBundle\Entity\VideoMediaEmbed;
use Oxa\VideoBundle\Repository\VideoMediaEmbedRepository;

/**
 * Class VideoMediaEmbedManager
 * @package Oxa\VideoBundle\Manager
 */
class VideoMediaEmbedManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var VideoEmbedAPIManager
     */
    private $videoEmbedAPIManager;

    /**
     * @var VideoMediaEmbedRepository
     */
    private $repository;

    /**
     * VideoMediaEmbedManager constructor.
     * @param EntityManager $entityManager
     * @param VideoEmbedAPIManager $videoEmbedAPIManager
     */
    public function __construct(EntityManager $entityManager, VideoEmbedAPIManager $videoEmbedAPIManager)
    {
        $this->em = $entityManager;
        $this->videoEmbedAPIManager = $videoEmbedAPIManager;

        $this->repository = $entityManager->getRepository(VideoMediaEmbed::class);
    }

    /**
     * @param VideoMedia $videoMedia
     * @param array $dimensions
     * @return string
     */
    public function getHTML(VideoMedia $videoMedia, array $dimensions)
    {
        $autoDimensions = true;

        if (0) {
        if (empty($dimensions)) {
            $embedObject = $this->getRepository()->findByMediaObjectWithoutDimensions($videoMedia);
        } elseif (!isset($dimensions['width']) && isset($dimensions['height'])) {
            $embedObject = $this->getRepository()->findByMediaObjectAndHeight($videoMedia, $dimensions['height']);
        } elseif (!isset($dimensions['height']) && isset($dimensions['width'])) {
            $embedObject = $this->getRepository()->findByMediaObjectAndWidth($videoMedia, $dimensions['width']);
        } else {
            $autoDimensions = false;

            $embedObject = $this->getRepository()->findByMediaObjectWithDimensions(
                $videoMedia,
                $dimensions['width'],
                $dimensions['height']
            );
        }
        }

        return 1;
        if ($embedObject !== null) {
            return $embedObject->getHTML();
        } else {
            try {
                $embedResponse = $this->getVideoEmbedAPIManager()->get($videoMedia->getHashedId(), $dimensions);
            } catch (\Exception $e) {
                return '';
            }

            $embedObject = $this->save($videoMedia, $embedResponse, $autoDimensions);
        }

        return $embedObject->getHtml();

    }

    /**
     * @param int $id
     * @return null|object
     */
    public function find(int $id)
    {
        return $this->getRepository()->find($id);
    }

    /**
     * @param VideoMedia $videoMedia
     * @param int $width
     * @param int $height
     * @return null|object
     */
    public function findEmbedForVideoMediaObjectWithDimensions(VideoMedia $videoMedia, int $width, int $height)
    {
        return $this->getRepository()->findByMediaObjectWithDimensions($videoMedia, $width, $height);
    }

    /**
     * @param VideoMedia $media
     * @param array $embedAPIData
     * @param bool $autoDimensions
     * @return VideoMediaEmbed
     */
    public function save(VideoMedia $media, array $embedAPIData, bool $autoDimensions = true) : VideoMediaEmbed
    {
        $videoMediaEmbed = new VideoMediaEmbed($embedAPIData, $media);
        $videoMediaEmbed->setAutoDimensions($autoDimensions);

        $this->em->persist($videoMediaEmbed);
        $this->em->flush();

        return $videoMediaEmbed;
    }

    /**
     * @return VideoEmbedAPIManager
     */
    private function getVideoEmbedAPIManager() : VideoEmbedAPIManager
    {
        return $this->videoEmbedAPIManager;
    }

    /**
     * @return VideoMediaEmbedRepository
     */
    private function getRepository() : VideoMediaEmbedRepository
    {
        return $this->repository;
    }
}
