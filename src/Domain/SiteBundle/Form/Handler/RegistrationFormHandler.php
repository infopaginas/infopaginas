<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 21.06.16
 * Time: 17:30
 */

namespace Domain\SiteBundle\Form\Handler;

use Domain\SiteBundle\Mailer\Mailer;
use FOS\UserBundle\Model\UserInterface;
use FOS\UserBundle\Model\UserManagerInterface;
use Oxa\Sonata\UserBundle\Entity\Group;
use Oxa\Sonata\UserBundle\Manager\GroupsManager;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RegistrationFormHandler
 * @package Domain\SiteBundle\Form\Handler
 */
class RegistrationFormHandler
{
    /** @var FormInterface  */
    protected $form;

    /** @var Request  */
    protected $request;

    /** @var UserManagerInterface */
    protected $userManager;

    /** @var GroupsManager */
    protected $groupsManager;

    /** @var Mailer */
    protected $mailer;

    /**
     * RegistrationFormHandler constructor.
     * @param FormInterface $form
     * @param Request $request
     * @param UserManagerInterface $userManager
     * @param GroupsManager $groupsManager
     */
    public function __construct(
        FormInterface $form,
        Request $request,
        UserManagerInterface $userManager,
        GroupsManager $groupsManager,
        Mailer $mailer
    ) {
        $this->form          = $form;
        $this->request       = $request;
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

        if ($this->request->getMethod() == 'POST') {
            $this->form->handleRequest($this->request);

            if ($this->form->isValid()) {
                $this->onSuccess($user);

                return true;
            }
        }

        return false;
    }

    /**
     * @param null $form
     * @return array
     */
    public function getErrors($form = null) : array
    {
        $errors = [];

        if ($form === null) {
            $form = $this->form;
        }

        if ($form->count()) {
            /** @var FormInterface $child */
            foreach ($form as $child) {
                if (!$child->isValid()) {
                    $errors[$child->getName()] = $this->getErrors($child);
                }
            }
        } else {
            /** @var FormError $error */
            foreach ($form->getErrors() as $error) {
                $errors[] = $error->getMessage();
            }
        }

        return $errors;
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

        $this->mailer->sendRegistrationCompleteEmailMessage($user);
    }
}
