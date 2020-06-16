<?php

namespace Domain\SiteBundle\Form\Handler;

use Domain\BusinessBundle\Form\Handler\BusinessFormHandlerInterface;
use Domain\SiteBundle\Mailer\Mailer;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Oxa\Sonata\UserBundle\Entity\Group;
use Oxa\Sonata\UserBundle\Manager\GroupsManager;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class RegistrationFormHandler
 * @package Domain\SiteBundle\Form\Handler
 */
class RegistrationFormHandler extends BaseFormHandler
{
    /** @var FormInterface  */
    protected $form;

    /** @var Request  */
    protected $requestStack;

    /** @var UserManagerInterface */
    protected $userManager;

    /** @var GroupsManager */
    protected $groupsManager;

    /** @var Mailer */
    protected $mailer;

    public function __construct(
        FormInterface $form,
        RequestStack $requestStack,
        UserManagerInterface $userManager,
        GroupsManager $groupsManager,
        Mailer $mailer
    ) {
        $this->form          = $form;
        $this->requestStack  = $requestStack;
        $this->userManager   = $userManager;
        $this->groupsManager = $groupsManager;
        $this->mailer        = $mailer;
    }

    /**
     * @return bool
     */
    public function process() : bool
    {
        $user = $this->userManager->createUser();
        $this->form->setData($user);

        if ($this->requestStack->getCurrentRequest()->getMethod() == 'POST') {
            $this->form->handleRequest($this->requestStack->getCurrentRequest());

            if ($this->form->isValid()) {
                $this->onSuccess($user);

                return true;
            }
        }

        return false;
    }

    /**
     * @param UserInterface $user
     */
    protected function onSuccess(UserInterface $user)
    {
        $consumerGroup = $this->groupsManager->findByCode(Group::CODE_CONSUMER);

        $user->setRole($consumerGroup);
        $user->setEnabled(true);

        $this->userManager->updateUser($user);
        $this->requestStack->getCurrentRequest()
            ->getSession()
            ->set(
                BusinessFormHandlerInterface::SUCCESSFUL_REGISTRATION_TEXT_KEY,
                BusinessFormHandlerInterface::SUCCESSFUL_REGISTRATION_TEXT_KEY
            );

        $this->mailer->sendRegistrationCompleteEmailMessage($user);
    }
}
