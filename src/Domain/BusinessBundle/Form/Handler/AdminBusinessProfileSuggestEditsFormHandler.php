<?php

namespace Domain\BusinessBundle\Form\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Domain\BusinessBundle\Entity\BusinessProfileSuggestEdit;
use Domain\BusinessBundle\Util\ArrayUtil;
use Domain\SiteBundle\Mailer\Mailer;
use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

/**
 * Class AdminBusinessProfileSuggestEditsFormHandler
 *
 * @package Domain\BusinessBundle\Form\Handler
 */
class AdminBusinessProfileSuggestEditsFormHandler extends BaseFormHandler implements BusinessFormHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Request */
    private $request;

    /** @var RouterInterface */
    private $router;

    /** @var Mailer */
    private $mailer;

    /* @var BusinessProfileSuggestEdit[] */
    private $acceptedSuggestEdits = [];

    /**
     * AdminBusinessProfileSuggestEditsFormHandler constructor.
     *
     * @param FormInterface          $form
     * @param Request                $request
     * @param EntityManagerInterface $em
     * @param RouterInterface        $router
     * @param Mailer                 $mailer
     */
    public function __construct(
        FormInterface $form,
        Request $request,
        EntityManagerInterface $em,
        RouterInterface $router,
        Mailer $mailer
    ) {
        $this->form    = $form;
        $this->request = $request;
        $this->em      = $em;
        $this->router  = $router;
        $this->mailer  = $mailer;
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function process()
    {
        if ($this->request->getMethod() == Request::METHOD_POST) {
            $this->form->handleRequest($this->request);

            if ($this->form->isValid()) {
                $this->onSuccess($this->form->get('suggestEdits')->getData());

                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    public function getRedirectRoute()
    {
        $businessProfileId = $this->request->attributes->get('businessProfile')->getId();

        if ($this->acceptedSuggestEdits) {
            foreach ($this->acceptedSuggestEdits as $suggestEdit) {
                if ($suggestEdit->getKey() === BusinessProfileSuggestEdit::KEY_MAP) {
                    $message = $suggestEdit->getValue();
                } else {
                    $message = $suggestEdit->getKeyLabel() . ': ' . $suggestEdit->getValue();
                }

                $this->request->getSession()->getFlashBag()->add(
                    'warning',
                    htmlentities($message)
                );
            }

            return $this->router->generate('admin_domain_business_businessprofile_edit', ['id' => $businessProfileId]);
        }

        return $this->router->generate('domain_admin_business_suggest_edits_business', ['id' => $businessProfileId]);
    }

    /**
     * @param array $formSuggestEdits
     */
    private function onSuccess(array $formSuggestEdits)
    {
        $suggestEditRepository = $this->em->getRepository(BusinessProfileSuggestEdit::class);
        $suggestEditList = $suggestEditRepository->getOpenedSuggestsByBusinessAndKey(
            $this->request->attributes->get('businessProfile'),
            $this->request->attributes->get('key')
        );
        $suggestEditList = ArrayUtil::useIdInKeys($suggestEditList);


        foreach ($formSuggestEdits as $id => $status) {
            $suggestEdit = $suggestEditList[$id];
            $suggestEdit->setStatus($status);

            if ($status === BusinessProfileSuggestEdit::STATUS_ACCEPTED) {
                $this->acceptedSuggestEdits[] = $suggestEdit;
            }

            $this->mailer->sendSuggestEditProcessedEmailMessage($suggestEdit);
        }

        $this->em->flush();
    }
}
