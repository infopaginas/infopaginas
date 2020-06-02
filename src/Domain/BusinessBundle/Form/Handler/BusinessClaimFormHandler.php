<?php

namespace Domain\BusinessBundle\Form\Handler;

use Domain\BusinessBundle\Manager\BusinessProfileManager;
use Domain\BusinessBundle\Manager\TasksManager;
use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Oxa\Sonata\UserBundle\Entity\User;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class ReviewFormHandler
 * @package Domain\BusinessBundle\Form\Handler
 */
class BusinessClaimFormHandler extends BaseFormHandler
{
    private const BUSINESS_NOT_FOUND_ERROR_MESSAGE = 'Business id not found';
    private const USER_NOT_FOUND_ERROR_MESSAGE = 'Unknown user';

    /** @var RequestStack $requestStack */
    private $requestStack;

    /** @var BusinessProfileManager $businessProfileManager */
    private $businessProfileManager;

    /** @var TasksManager $tasksManager */
    private $tasksManager;

    public function __construct(FormInterface $form, RequestStack $requestStack, ContainerInterface $container)
    {
        $this->form         = $form;
        $this->requestStack = $requestStack;
        $this->container    = $container;

        $this->businessProfileManager = $this->container->get('domain_business.manager.business_profile');
        $this->tasksManager           = $this->container->get('domain_business.manager.tasks');

        $tokenStorage      = $this->container->get('security.token_storage');
        $this->currentUser = $tokenStorage->getToken()->getUser();
    }

    /**
     * @return bool
     */
    public function process()
    {
        if ($this->requestStack->getCurrentRequest()->getMethod() == Request::METHOD_POST) {
            $this->form->handleRequest($this->requestStack->getCurrentRequest());

            if ($this->form->isValid()) {
                $this->onSuccess();
                return true;
            }
        }

        return false;
    }

    private function onSuccess()
    {
        $businessProfileId = $this->requestStack->getCurrentRequest()->request->get('businessProfileId', false);

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
