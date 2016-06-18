<?php
/**
 * Created by PhpStorm.
 * User: Xedin
 * Date: 18.06.16
 * Time: 12:34
 */

namespace Oxa\WistiaBundle\Manager;


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Oxa\WistiaBundle\Entity\WistiaMedia;
use Oxa\WistiaBundle\Entity\WistiaMediaEmbed;
use Oxa\WistiaBundle\Repository\WistiaMediaEmbedRepository;

/**
 * Class WistiaMediaEmbedManager
 * @package Oxa\WistiaBundle\Manager
 */
class WistiaMediaEmbedManager
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var WistiaEmbedAPIManager
     */
    private $wistiaEmbedAPIManager;

    /**
     * @var WistiaMediaEmbedRepository
     */
    private $repository;

    /**
     * WistiaMediaEmbedManager constructor.
     * @param EntityManager $entityManager
     * @param WistiaEmbedAPIManager $wistiaEmbedAPIManager
     */
    public function __construct(EntityManager $entityManager, WistiaEmbedAPIManager $wistiaEmbedAPIManager)
    {
        $this->em = $entityManager;
        $this->wistiaEmbedAPIManager = $wistiaEmbedAPIManager;

        $this->repository = $entityManager->getRepository(WistiaMediaEmbedRepository::SLUG);
    }

    /**
     * @param WistiaMedia $wistiaMedia
     * @param array $dimensions
     * @return string
     */
    public function getHTML(WistiaMedia $wistiaMedia, array $dimensions)
    {
        $autoDimensions = true;

        if (empty($dimensions)) {
            $embedObject = $this->getRepository()->findByMediaObjectWithoutDimensions($wistiaMedia);
        } elseif (!isset($dimensions['width']) && isset($dimensions['height'])) {
            $embedObject = $this->getRepository()->findByMediaObjectAndHeight($wistiaMedia, $dimensions['height']);
        } elseif (!isset($dimensions['height']) && isset($dimensions['width'])) {
            $embedObject = $this->getRepository()->findByMediaObjectAndWidth($wistiaMedia, $dimensions['width']);
        } else {
            $autoDimensions = false;

            $embedObject = $this->getRepository()->findByMediaObjectWithDimensions(
                $wistiaMedia,
                $dimensions['width'],
                $dimensions['height']
            );
        }

        if ($embedObject !== null) {
            return $embedObject->getHTML();
        } else {
            $embedResponse = $this->getWistiaEmbedAPIManager()->get($wistiaMedia->getHashedId(), $dimensions);
            $embedObject = $this->save($wistiaMedia, $embedResponse, $autoDimensions);
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
     * @param WistiaMedia $wistiaMedia
     * @param int $width
     * @param int $height
     * @return null|object
     */
    public function findEmbedForWistiaMediaObjectWithDimensions(WistiaMedia $wistiaMedia, int $width, int $height)
    {
        return $this->getRepository()->findByMediaObjectWithDimensions($wistiaMedia, $width, $height);
    }

    /**
     * @param WistiaMedia $media
     * @param array $embedAPIData
     * @param bool $autoDimensions
     * @return WistiaMediaEmbed
     */
    public function save(WistiaMedia $media, array $embedAPIData, bool $autoDimensions = true) : WistiaMediaEmbed
    {
        $wistiaMediaEmbed = new WistiaMediaEmbed($embedAPIData, $media);
        $wistiaMediaEmbed->setAutoDimensions($autoDimensions);

        $this->em->persist($wistiaMediaEmbed);
        $this->em->flush();

        return $wistiaMediaEmbed;
    }

    /**
     * @return WistiaEmbedAPIManager
     */
    private function getWistiaEmbedAPIManager() : WistiaEmbedAPIManager
    {
        return $this->wistiaEmbedAPIManager;
    }

    /**
     * @return WistiaMediaEmbedRepository
     */
    private function getRepository() : WistiaMediaEmbedRepository
    {
        return $this->repository;
    }
}