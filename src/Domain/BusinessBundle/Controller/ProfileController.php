<?php

namespace Domain\BusinessBundle\Controller;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Review\BusinessReview;
use Domain\BusinessBundle\Form\Handler\BusinessProfileFormHandler;
use Domain\BusinessBundle\Form\Type\BusinessCloseRequestType;
use Domain\BusinessBundle\Form\Type\BusinessProfileFormType;
use Domain\BusinessBundle\Form\Type\BusinessReviewType;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Manager\BusinessReviewManager;
use Domain\BusinessBundle\Util\Traits\JsonResponseBuilderTrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Domain\BannerBundle\Model\TypeInterface;

/**
 * Class ProfileController
 * @package Domain\BusinessBundle\Controller
 */
class ProfileController extends Controller
{
    use JsonResponseBuilderTrait;

    const SUCCESS_PROFILE_REQUEST_CREATED_MESSAGE = 'Business Profile Request send. Please wait for approval';
    const SUCCESS_PROFILE_CLOSE_REQUEST_CREATED_MESSAGE = 'Close Profile Request send. Please wait for approval';

    const ERROR_VALIDATION_FAILURE = 'Validation Failure.';

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createAction()
    {
        $businessProfileForm = $this->getBusinessProfileForm();

        return $this->render('DomainBusinessBundle:Profile:edit.html.twig', [
            'businessProfileForm' => $businessProfileForm->createView(),
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, int $id)
    {
        $locale = $request->request->get('locale', BusinessProfile::DEFAULT_LOCALE);

        /** @var BusinessProfile $businessProfile */
        $businessProfile = $this->getBusinessProfilesManager()->find($id, $locale);

        $businessProfileForm = $this->getBusinessProfileForm($businessProfile);

        //return form-only for AJAX requests
        if (!$request->isXmlHttpRequest()) {
            $template = 'DomainBusinessBundle:Profile:edit.html.twig';
        } else {
            $template = 'DomainBusinessBundle:Profile/blocks:edit_form.html.twig';
        }

        $closeBusinessProfileForm = $this->createForm(new BusinessCloseRequestType());

        return $this->render($template, [
            'businessProfileForm' => $businessProfileForm->createView(),
            'businessProfile'     => $businessProfile,
            'closeBusinessProfileForm' => $closeBusinessProfileForm->createView(),
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
        } catch (Exception $e) {
            return $this->getFailureResponse($e->getMessage(), [], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->getFailureResponse(self::ERROR_VALIDATION_FAILURE, $formHandler->getErrors());
    }

    /**
     * @param Request $request
     * @param string $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $request, string $slug)
    {
        /** @var BusinessProfile $businessProfile */
        $businessProfile = $this->getBusinessProfilesManager()->findBySlug($slug);

        $photos         = $this->getBusinessProfilesManager()->getBusinessProfilePhotoImages($businessProfile);
        $advertisements = $this->getBusinessProfilesManager()->getBusinessProfileAdvertisementImages($businessProfile);

        $lastReview       = $this->getBusinessProfilesManager()->getLastReviewForBusinessProfile($businessProfile);
        $reviewForm       = $this->getBusinessReviewForm();
        $reviewsCount     = $this->getBusinessProfilesManager()->getReviewsCountForBusinessProfile($businessProfile);
        $reviewsAvgRating = $this->getBusinessProfilesManager()
            ->calculateReviewsAvgRatingForBusinessProfile($businessProfile);

        $bannerFactory  = $this->get('domain_banner.factory.banner');

        $bannerFactory->prepearBanners(array(
            TypeInterface::CODE_PORTAL,
        ));

        return $this->render('DomainBusinessBundle:Profile:show.html.twig', [
            'businessProfile'  => $businessProfile,
            'photos'           => $photos,
            'advertisements'   => $advertisements,
            'lastReview'       => $lastReview,
            'reviewForm'       => $reviewForm->createView(),
            'reviewsCount'     => $reviewsCount,
            'reviewsAvgRating' => $reviewsAvgRating,
            'bannerFactory'    => $bannerFactory,
        ]);
    }

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
     * @return \Symfony\Component\Form\Form
     */
    private function getBusinessReviewForm()
    {
        return $this->createForm(new BusinessReviewType());
    }

    /**
     * @return BusinessProfileManager
     */
    private function getBusinessProfilesManager() : BusinessProfileManager
    {
        return $this->get('domain_business.manager.business_profile');
    }

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
     * @param bool $businessProfile
     * @return FormInterface
     */
    private function getBusinessProfileForm($businessProfile = false) : FormInterface
    {
        if ($businessProfile === false) {
            $businessProfile = $this->getBusinessProfilesManager()->createProfile();
        }

        return $this->createForm(new BusinessProfileFormType(), $businessProfile);
    }
}
