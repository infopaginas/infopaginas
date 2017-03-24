<?php

namespace Domain\BusinessBundle\Manager;

use Doctrine\ORM\EntityManager;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Model\DataType\ReviewsResultsDTO;
use Oxa\ManagerArchitectureBundle\Model\DataType\AbstractDTO;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class VideoManager
 *
 * @package Domain\BusinessBundle\Manager
 */
class VideoManager
{
    const VIDEOS_HOMEPAGE_LIMIT = 2;

    private $em;

    private $repository;

    public function __construct(EntityManager $entityManager)
    {
        $this->em = $entityManager;

        $this->repository = $entityManager->getRepository(BusinessProfile::class);
    }

    public function fetchHomepageVideos()
    {
        $videos = $this->getRepository()->getHomepageVideos(self::VIDEOS_HOMEPAGE_LIMIT);
        return $videos;
    }

    public function getActiveVideos()
    {
        $videos = $this->getRepository()->getVideos();

        return $videos;
    }

    public function getBusinessProfilesByVideosUpdate($searchParams)
    {
        $videos = $this->getRepository()->getBusinessProfilesByVideosUpdate($searchParams);

        return $videos;
    }

    /**
     * @param AbstractDTO $paramsDTO
     * @return ReviewsResultsDTO
     */
    public function getVideosResultDTO(AbstractDTO $paramsDTO)
    {
        $results = $this->getRepository()->getBusinessProfilesByVideosUpdate($paramsDTO);

        $totalResults = $this->getRepository()->countBusinessProfilesByVideosUpdate();

        $pagesCount = ceil($totalResults / $paramsDTO->limit);

        return new ReviewsResultsDTO($results, $totalResults, $paramsDTO->page, $pagesCount);
    }

    public function getVideosSeoData(ContainerInterface $container)
    {
        $translator  = $container->get('translator');
        $seoSettings = $container->getParameter('seo_custom_settings');

        $companyName          = $seoSettings['company_name'];
        $titleMaxLength       = $seoSettings['title_max_length'];
        $descriptionMaxLength = $seoSettings['description_max_length'];

        $seoTitle = $translator->trans('Videos');

        $seoDescription = $seoTitle;

        $seoTitle = $seoTitle . ' | ' . $companyName;

        $seoData = [
            'seoTitle' => mb_substr($seoTitle, 0, $titleMaxLength),
            'seoDescription' => mb_substr($seoDescription, 0, $descriptionMaxLength),
        ];

        return $seoData;
    }

    private function getRepository()
    {
        return $this->repository;
    }
}
