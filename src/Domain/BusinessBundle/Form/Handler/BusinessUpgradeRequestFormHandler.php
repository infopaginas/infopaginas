<?php

namespace Domain\BusinessBundle\Form\Handler;

use Domain\BusinessBundle\Form\Type\BusinessUpgradeRequestType;
use Domain\SiteBundle\Mailer\Mailer;
use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Oxa\Sonata\UserBundle\Entity\User;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class BusinessUpgradeRequestFormHandler
 *
 * @package Domain\BusinessBundle\Form\Handler
 */
class BusinessUpgradeRequestFormHandler extends BaseFormHandler implements BusinessFormHandlerInterface
{
    private const MESSAGE_BUSINESS_PROFILE_UPGRADE = 'business_profile.message.upgrade';

    /* @var RequestStack */
    private $requestStack;

    /* @var Mailer */
    private $mailer;

    /* @var User */
    private $currentUser;

    /* @var TranslatorInterface */
    private $translator;

    public function __construct(
        FormInterface $form,
        RequestStack $requestStack,
        Mailer $mailer,
        TokenStorageInterface $tokenStorage,
        TranslatorInterface $translator
    ) {
        $this->form         = $form;
        $this->requestStack = $requestStack;
        $this->mailer       = $mailer;
        $this->currentUser  = $tokenStorage->getToken()->getUser();
        $this->translator   = $translator;
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
        $data = $this->form->getData();
        $data['time'] = BusinessUpgradeRequestType::TIME_CHOICES[$data['time']];
        $this->mailer->sendUpdateProfileRequestMessage($this->currentUser, $data);

        $session = $this->requestStack->getCurrentRequest()->getSession();
        $session->getFlashBag()->add(
            self::MESSAGE_BUSINESS_PROFILE_FLASH_GROUP,
            $this->translator->trans(self::MESSAGE_BUSINESS_PROFILE_UPGRADE)
        );
    }
}
