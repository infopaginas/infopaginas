<?php

namespace Domain\BusinessBundle\Controller;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Domain\BusinessBundle\Entity\Category;
use Domain\BusinessBundle\Form\Handler\BusinessClaimFormHandler;
use Domain\BusinessBundle\Form\Type\BusinessClaimRequestType;
use Domain\BusinessBundle\Model\DayOfWeekModel;
use Domain\BusinessBundle\Util\BusinessProfileUtil;
use Domain\ReportBundle\Manager\CategoryReportManager;
use Domain\SiteBundle\Utils\Helpers\LocaleHelper;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Form\Handler\BusinessProfileFormHandler;
use Domain\BusinessBundle\Form\Type\BusinessCloseRequestType;
use Domain\BusinessBundle\Form\Type\BusinessProfileFormType;
use Domain\BusinessBundle\Form\Type\BusinessReviewType;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Util\Traits\JsonResponseBuilderTrait;
use Domain\ReportBundle\Manager\BusinessOverviewReportManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Domain\BannerBundle\Model\TypeInterface;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Oxa\Sonata\UserBundle\Entity\User;

/**
 * Class ProfileController
 * @package Domain\BusinessBundle\Controller
 */
class ProfileController extends Controller
{
    use JsonResponseBuilderTrait;

    const SUCCESS_PROFILE_REQUEST_CREATED_MESSAGE = 'Business Profile Request send. Please wait for approval';
    const SUCCESS_PROFILE_CLOSE_REQUEST_CREATED_MESSAGE = 'Close Profile Request send. Please wait for approval';
    const SUCCESS_PROFILE_CLAIM_REQUEST_CREATED_MESSAGE = 'claim_business.response.success';

    const ERROR_VALIDATION_FAILURE = 'Validation Failure.';
    const ERROR_EMAIL_ALREADY_USED = 'Email is already in use. Please put another';
    const ERROR_ACCESS_NOT_ALLOWED = 'You haven\'t access to this page!';

    /**
     * @return array
     */
    protected function getMediaContextTypes()
    {
        $types = [
            OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO        => 'Logo',
            OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_BACKGROUND  => 'Background',
            OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_IMAGES      => 'Photo',
        ];

        return $types;
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        $businessProfileForm = $this->getBusinessProfileForm();

        return $this->render(':redesign:business-profile-edit.html.twig', [
            'businessProfileForm' => $businessProfileForm->createView(),
            'mediaContextTypes'   => $this->getMediaContextTypes(),
            'localeBlocks'        => LocaleHelper::getLocaleList(),
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, int $id)
    {
        $locale = LocaleHelper::getLocale($request->getLocale());

        /** @var BusinessProfile $businessProfile */
        $businessProfile = $this->getBusinessProfilesManager()->find($id, $locale);

        if (!$businessProfile or !$businessProfile->getIsActive()) {
            throw $this->createNotFoundException();
        }

        $this->checkBusinessProfileAccess($businessProfile);

        $businessProfileForm      = $this->getBusinessProfileForm($businessProfile);
        $closeBusinessProfileForm = $this->createForm(new BusinessCloseRequestType());

        return $this->render(':redesign:business-profile-edit.html.twig', [
            'businessProfileForm'      => $businessProfileForm->createView(),
            'businessProfile'          => $businessProfile,
            'closeBusinessProfileForm' => $closeBusinessProfileForm->createView(),
            'logoTypeConstant'         => OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_LOGO,
            'photoTypeConstant'        => OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_IMAGES,
            'backgroundTypeConstant'   => OxaMediaInterface::CONTEXT_BUSINESS_PROFILE_BACKGROUND,
            'mediaContextTypes'        => $this->getMediaContextTypes(),
            'localeBlocks'             => LocaleHelper::getLocaleList(),
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function saveAction()
    {
        $formHandler = $this->getBusinessProfileFormHandler();

        try {
            if ($formHandler->process()) {
                return $this->getSuccessResponse(self::SUCCESS_PROFILE_REQUEST_CREATED_MESSAGE);
            }
        } catch (UniqueConstraintViolationException $e) {
            return $this->getFailureResponse(
                self::ERROR_EMAIL_ALREADY_USED,
                $formHandler->getErrors(),
                Response::HTTP_INTERNAL_SERVER_ERROR
            );
        } catch (\Exception $e) {
            return $this->getFailureResponse($e->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->getFailureResponse(self::ERROR_VALIDATION_FAILURE, $formHandler->getErrors());
    }

    /**
     * @param Request $request
     * @param string $citySlug
     * @param string $slug
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $request, $citySlug, string $slug)
    {
        /** @var BusinessProfile $businessProfile */
        $businessProfile = $this->getBusinessProfilesManager()->findBySlug($slug);

        if (!$businessProfile) {
            $businessProfileAlias = $this->getBusinessProfilesManager()->findByAlias($slug);

            if ($businessProfileAlias) {
                return $this->redirectToRoute(
                    'domain_business_profile_view',
                    [
                        'citySlug' => $businessProfileAlias->getCitySlug(),
                        'slug'     => $businessProfileAlias->getSlug(),
                    ],
                    301
                );
            } else {
                throw new \Symfony\Component\HttpKernel\Exception\GoneHttpException();
            }
        } elseif (!$businessProfile->getIsActive()) {
            throw $this->createNotFoundException();
        }

        $catalogLocalitySlug = $businessProfile->getCitySlug();

        if ($catalogLocalitySlug != $citySlug or $slug != $businessProfile->getSlug()) {
            return $this->redirectToRoute(
                'domain_business_profile_view',
                [
                    'citySlug' => $catalogLocalitySlug,
                    'slug'     => $businessProfile->getSlug(),
                ],
                301
            );
        }

        $this->getBusinessOverviewReportManager()->registerBusinessView([$businessProfile]);

        $dcDataDTO = $this->getBusinessProfilesManager()->getSlugDcDataDTO($businessProfile);
        $locale    = LocaleHelper::getLocale($request->getLocale());

        $photos         = $this->getBusinessProfilesManager()->getBusinessProfilePhotoImages($businessProfile, $locale);
        $advertisements = $this->getBusinessProfilesManager()->getBusinessProfileAdvertisementImages(
            $businessProfile,
            $locale
        );

        $lastReview       = $this->getBusinessProfilesManager()->getLastReviewForBusinessProfile($businessProfile);
        $reviewForm       = $this->getBusinessReviewForm();

        if (!$businessProfile->getHideMap()) {
            $locationMarkers  = $this->getBusinessProfilesManager()
                ->getLocationMarkersFromProfileData([$businessProfile]);
        } else {
            $locationMarkers = [];
        }

        $bannerManager  = $this->get('domain_banner.manager.banner');
        $banners        = $bannerManager->getBanners(
            [
                TypeInterface::CODE_BUSINESS_PAGE_RIGHT,
                TypeInterface::CODE_BUSINESS_PAGE_BOTTOM,
            ]
        );

        $schema = $this->getBusinessProfilesManager()->buildBusinessProfilesSchema([$businessProfile], true);

        $this->getCategoryReportManager()->registerBusinessVisit($businessProfile);

        $showClaimBlock =  $this->getBusinessProfilesManager()->getClaimButtonPermitted($businessProfile);

        if ($showClaimBlock) {
            $claimBusinessForm = $this->createForm(new BusinessClaimRequestType())->createView();
        } else {
            $claimBusinessForm = null;
        }

        return $this->render(':redesign:business-profile.html.twig', [
            'businessProfile' => $businessProfile,
            'seoData'         => $businessProfile,
            'seoTags'         => BusinessProfileUtil::getSeoTags(BusinessProfileUtil::SEO_CLASS_PREFIX_PROFILE),
            'photos'          => $photos,
            'advertisements'  => $advertisements,
            'lastReview'      => $lastReview,
            'reviewForm'      => $reviewForm->createView(),
            'banners'         => $banners,
            'dcDataDTO'       => $dcDataDTO,
            'schemaJsonLD'    => $schema,
            'markers'         => $locationMarkers,
            'showClaimButton' => $showClaimBlock,
            'claimBusinessForm' => $claimBusinessForm,
        ]);
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function claimAction(Request $request) : JsonResponse
    {
        $formHandler = $this->getBusinessClaimFormHandler();

        try {
            if ($formHandler->process()) {
                return $this->getSuccessResponse(self::SUCCESS_PROFILE_CLAIM_REQUEST_CREATED_MESSAGE);
            }
        } catch (\Exception $e) {
            return $this->getFailureResponse($e->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->getFailureResponse(self::ERROR_VALIDATION_FAILURE, $formHandler->getErrors());
    }

    /**
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function closeAction(Request $request)
    {
        $formHandler = $this->getBusinessProfileCloseRequestFormHandler();

        try {
            if ($formHandler->process()) {
                return $this->getSuccessResponse(self::SUCCESS_PROFILE_CLOSE_REQUEST_CREATED_MESSAGE);
            }
        } catch (Exception $e) {
            return $this->getFailureResponse($e->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->getFailureResponse(self::ERROR_VALIDATION_FAILURE, $formHandler->getErrors());
    }

    /**
     * @param Request  $request
     * @param int|null $businessProfileId
     *
     * @return JsonResponse
     */
    public function localityListAction(Request $request, $businessProfileId = null)
    {
        $areas  = $request->request->get('areas', []);
        $locale = LocaleHelper::getLocale($request->getLocale());
        $currentLocale = $request->request->get('currentLocale', $locale);

        if (!$areas) {
            return new JsonResponse(['data' => []]);
        }

        $businessProfilesManager = $this->getBusinessProfilesManager();

        $localities = $businessProfilesManager->getAreaLocalities($businessProfileId, $areas, $currentLocale);

        return new JsonResponse(['data' => $localities]);
    }

    /**
     * @param Request  $request
     * @param int|null $businessProfileId
     *
     * @return JsonResponse
     */
    public function neighborhoodListAction(Request $request, $businessProfileId = null)
    {
        $localities = $request->request->get('localities', []);
        $locale     = LocaleHelper::getLocale($request->getLocale());
        $currentLocale = $request->request->get('currentLocale', $locale);

        if (!$localities) {
            return new JsonResponse(['data' => []]);
        }

        $businessProfilesManager = $this->getBusinessProfilesManager();

        $neighborhoods = $businessProfilesManager->getLocalitiesNeighborhoods(
            $businessProfileId,
            $localities,
            $currentLocale
        );

        return new JsonResponse(['data' => $neighborhoods]);
    }

    /**
     * @param Request  $request
     *
     * @return JsonResponse
     */
    public function categoryAutocompleteAction(Request $request)
    {
        $query = $request->query->get('q', '');
        $locale = LocaleHelper::getLocale($request->getLocale());

        $businessProfileManager = $this->get('domain_business.manager.business_profile');
        $results = $businessProfileManager->searchCategoryAutosuggestByPhrase(
            $query,
            $locale
        );

        return new JsonResponse($results);
    }

    /**
     * @return \Symfony\Component\Form\Form
     */
    private function getBusinessReviewForm()
    {
        return $this->createForm(new BusinessReviewType());
    }

    /**
     * @return CategoryReportManager
     */
    protected function getCategoryReportManager() : CategoryReportManager
    {
        return $this->get('domain_report.manager.category_report_manager');
    }

    /**
     * @return BusinessProfileManager
     */
    private function getBusinessProfilesManager() : BusinessProfileManager
    {
        return $this->get('domain_business.manager.business_profile');
    }

    /**
     * @return \Domain\BusinessBundle\Form\Handler\BusinessCloseRequestFormHandler
     */
    private function getBusinessProfileCloseRequestFormHandler()
    {
        return $this->get('domain_business.form.handler.business_close_request');
    }

    /**
     * @return BusinessProfileFormHandler
     */
    private function getBusinessProfileFormHandler() : BusinessProfileFormHandler
    {
        return $this->get('domain_business.form.handler.business_profile');
    }

    /**
     * @return BusinessOverviewReportManager
     */
    protected function getBusinessOverviewReportManager() : BusinessOverviewReportManager
    {
        return $this->get('domain_report.manager.business_overview_report_manager');
    }

    /**
     * @param BusinessProfile $businessProfile
     * @throws \Exception
     */
    protected function checkBusinessProfileAccess(BusinessProfile $businessProfile)
    {
        $token = $this->get('security.context')->getToken();
        if (!$token) {
            throw $this->createNotFoundException(self::ERROR_ACCESS_NOT_ALLOWED);
        }

        $user = $token->getUser();

        if (!$user || !$user instanceof User) {
            throw $this->createNotFoundException(self::ERROR_ACCESS_NOT_ALLOWED);
        }

        if (!$user->getBusinessProfiles()->contains($businessProfile)) {
            throw $this->createNotFoundException(self::ERROR_ACCESS_NOT_ALLOWED);
        }
    }

    /**
     * @param bool $businessProfile
     *
     * @return FormInterface
     */
    private function getBusinessProfileForm($businessProfile = false) : FormInterface
    {
        if ($businessProfile === false) {
            $businessProfile = $this->getBusinessProfilesManager()->createProfile();
        }

        return $this->get('domain_business.form.business_profile')->setData($businessProfile);
    }

    /**
     * @return BusinessClaimFormHandler
     */
    private function getBusinessClaimFormHandler()
    {
        return $this->get('domain_business.form.handler.claim');
    }
}
