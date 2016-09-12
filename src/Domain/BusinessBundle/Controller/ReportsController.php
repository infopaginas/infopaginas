<?php

namespace Domain\BusinessBundle\Controller;

use AntiMattr\GoogleBundle\Analytics\Impression;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\ReportBundle\Entity\Keyword;
use Domain\ReportBundle\Google\Analytics\DataFetcher;
use Domain\ReportBundle\Model\DataType\ReportDatesRangeVO;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class ReportController
 * @package Domain\BusinessBundle\Controller
 */
class ReportsController extends Controller
{
    const BUSINESS_NOT_FOUND_MESSAGE = 'Business profile is not found.';

    /**
     * @param Request $request
     * @param int $businessProfileId
     * @return JsonResponse
     * @throws \Exception
     */
    public function overviewAction(Request $request, int $businessProfileId)
    {
        /** @var BusinessProfile $businessProfile */
        $businessProfile = $this->getBusinessProfileManager()->find($businessProfileId);

        if (!$businessProfile) {
            throw new NotFoundHttpException(self::BUSINESS_NOT_FOUND_MESSAGE);
        }

        $path = $this->generateUrl('domain_business_profile_view', [
            'slug'     => $businessProfile->getSlug(),
            'citySlug' => $businessProfile->getCitySlug(),
        ]);

        //only for dev env - remove app_dev.php from URL (GA doesn't track it)
        if ($this->get('kernel')->getEnvironment() == 'dev') {
            $path = str_replace('/app_dev.php', '', $path);
        }

        $dateRange = new ReportDatesRangeVO(new \DateTime('-1week'), new \DateTime());

        $weeklyBusinessViews = $this->getGADataFetcher()->getViews($path, $dateRange);

        return new JsonResponse($weeklyBusinessViews);
    }

    /**
     * @param Request $request
     * @param int $businessProfileId
     * @return JsonResponse
     */
    public function impressionsAction(Request $request, int $businessProfileId)
    {
        /** @var BusinessProfile $businessProfile */
        $businessProfile = $this->getBusinessProfileManager()->find($businessProfileId);

        if (!$businessProfile) {
            throw new NotFoundHttpException(self::BUSINESS_NOT_FOUND_MESSAGE);
        }

        $dateRange = new ReportDatesRangeVO(new \DateTime('-1week'), new \DateTime());

        $weeklyBusinessImpressions = $this->getGADataFetcher()->getImpressions($businessProfile->getSlug(), $dateRange);

        return new JsonResponse($weeklyBusinessImpressions);
    }

    public function keywordsAction(Request $request, int $businessProfileId)
    {
        /** @var BusinessProfile $businessProfile */
        $businessProfile = $this->getBusinessProfileManager()->find($businessProfileId);

        if (!$businessProfile) {
            throw new NotFoundHttpException(self::BUSINESS_NOT_FOUND_MESSAGE);
        }

        $repo = $this->getDoctrine()->getRepository(Keyword::class);
        dump($repo->getTopKeywordsForBusinessProfile($businessProfile));
        die();

        //$dateRange = new ReportDatesRangeVO(new \DateTime('-1week'), new \DateTime());
    }

    /**
     * @return DataFetcher
     */
    private function getGADataFetcher() : DataFetcher
    {
        return $this->get('domain_report.google_analytics.data_fetcher');
    }

    /**
     * @return BusinessProfileManager
     */
    private function getBusinessProfileManager() : BusinessProfileManager
    {
        return $this->get('domain_business.manager.business_profile');
    }
}
