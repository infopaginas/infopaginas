<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 12.08.16
 * Time: 11:24
 */

namespace Domain\BusinessBundle\Form\Handler;

use Domain\BusinessBundle\Entity\Review\BusinessReview;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Manager\BusinessReviewManager;
use Domain\BusinessBundle\Manager\TasksManager;
use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Oxa\ManagerArchitectureBundle\Model\Interfaces\FormHandlerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class ReviewFormHandler
 * @package Domain\BusinessBundle\Form\Handler
 */
class ReviewFormHandler extends BaseFormHandler implements FormHandlerInterface
{
    const BUSINESS_NOT_FOUND_ERROR_MESSAGE = 'Business id is not found';

    /** @var Request $request */
    private $request;

    /** @var BusinessReviewManager $manager */
    private $manager;

    /** @var BusinessProfileManager $businessProfileManager */
    private $businessProfileManager;

    /** @var TasksManager $tasksManager */
    private $tasksManager;

    /** @var TokenStorageInterface $tokenStorage */
    private $tokenStorage;

    public function __construct(
        FormInterface $form,
        Request $request,
        BusinessReviewManager $manager,
        BusinessProfileManager $businessProfileManager,
        TasksManager $tasksManager,
        TokenStorageInterface $tokenStorage
    ) {
        $this->form                   = $form;
        $this->request                = $request;
        $this->manager                = $manager;
        $this->businessProfileManager = $businessProfileManager;
        $this->tasksManager           = $tasksManager;
        $this->tokenStorage           = $tokenStorage;
    }

    public function process()
    {
        if ($this->request->getMethod() == 'POST') {
            $this->form->handleRequest($this->request);

            /** @var BusinessReview $review */
            $review = $this->form->getData();

            if ($this->form->isValid()) {
                $this->onSuccess($review);
                return true;
            }
        }

        return false;
    }

    private function onSuccess(BusinessReview $review)
    {
        $review->setUser($this->getCurrentUser());

        $businessProfileId = $this->getRequest()->request->get('businessProfileId', false);

        if (!$businessProfileId) {
            throw new \Exception(self::BUSINESS_NOT_FOUND_ERROR_MESSAGE);
        }

        $businessProfile = $this->getBusinessProfileManager()->find($businessProfileId);

        if (!$businessProfile) {
            throw new \Exception(self::BUSINESS_NOT_FOUND_ERROR_MESSAGE);
        }

        $review->setBusinessProfile($businessProfile);

        $username = $this->getBusinessReviewManager()->computeReviewerUsername($review);
        $review->setUsername($username);

        $this->getBusinessReviewManager()->save($review);

        $this->getTasksManager()->createBusinessReviewConfirmationRequest($review);
    }

    private function getRequest() : Request
    {
        return $this->request;
    }

    private function getBusinessProfileManager() : BusinessProfileManager
    {
        return $this->businessProfileManager;
    }

    private function getBusinessReviewManager() : BusinessReviewManager
    {
        return $this->manager;
    }

    private function getTasksManager() : TasksManager
    {
        return $this->tasksManager;
    }

    private function getCurrentUser()
    {
        return $this->tokenStorage->getToken()->getUser();
    }
}
