<?php

namespace Domain\BusinessBundle\Form\Handler;

use Domain\BusinessBundle\Entity\Review\BusinessReview;
use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Manager\BusinessReviewManager;
use Domain\BusinessBundle\Manager\TasksManager;
use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Oxa\ManagerArchitectureBundle\Model\Interfaces\FormHandlerInterface;
use Oxa\Sonata\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class ReviewFormHandler
 * @package Domain\BusinessBundle\Form\Handler
 */
class BusinessClaimFormHandler extends BaseFormHandler
{
    const BUSINESS_NOT_FOUND_ERROR_MESSAGE = 'Business id not found';
    const USER_NOT_FOUND_ERROR_MESSAGE = 'Unknown user';

    /** @var Request $request */
    private $request;

    /** @var BusinessProfileManager $businessProfileManager */
    private $businessProfileManager;

    /** @var TasksManager $tasksManager */
    private $tasksManager;

    public function __construct(FormInterface $form, Request $request, ContainerInterface $container)
    {
        $this->form               = $form;
        $this->request            = $request;
        $this->container          = $container;

        $this->businessProfileManager = $this->container->get('domain_business.manager.business_profile');
        $this->tasksManager           = $this->container->get('domain_business.manager.tasks');

        $tokenStorage             = $this->container->get('security.token_storage');
        $this->currentUser        = $tokenStorage->getToken()->getUser();
    }

    /**
     * @return bool
     */
    public function process()
    {
        if ($this->request->getMethod() == Request::METHOD_POST) {
            $this->form->handleRequest($this->request);

            if ($this->form->isValid()) {
                $this->onSuccess();
                return true;
            }
        }

        return false;
    }

    private function onSuccess()
    {
        $businessProfileId = $this->request->request->get('businessProfileId', false);

        if (!($this->currentUser instanceof User)) {
            throw new \Exception(self::USER_NOT_FOUND_ERROR_MESSAGE);
        }

        if (!$businessProfileId) {
            throw new \Exception(self::BUSINESS_NOT_FOUND_ERROR_MESSAGE);
        }

        $businessProfile = $this->businessProfileManager->find($businessProfileId);

        if (!$businessProfile) {
            throw new \Exception(self::BUSINESS_NOT_FOUND_ERROR_MESSAGE);
        }

        $message = $this->form->get('message')->getData();

        $this->tasksManager->createClaimProfileConfirmationRequest($businessProfile, $message);
    }
}
