<?php

namespace Domain\BusinessBundle\Form\Handler;

use Doctrine\ORM\EntityManagerInterface;
use Domain\BusinessBundle\Entity\BusinessProfileSuggestEdit;
use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BusinessProfileSuggestEditFormHandler
 *
 * @package Domain\BusinessBundle\Form\Handler
 */
class BusinessProfileSuggestEditsFormHandler extends BaseFormHandler implements BusinessFormHandlerInterface
{
    /** @var EntityManagerInterface */
    private $em;

    /** @var Request */
    private $request;

    /**
     * BusinessProfileSuggestEditsFormHandler constructor.
     *
     * @param FormInterface          $form
     * @param Request                $request
     * @param EntityManagerInterface $em
     */
    public function __construct(FormInterface $form, Request $request, EntityManagerInterface $em)
    {
        $this->form    = $form;
        $this->request = $request;
        $this->em      = $em;
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
                $this->onSuccess($this->form->getData());

                return true;
            }
        }

        return false;
    }

    /**
     * @param array $formData
     */
    private function onSuccess(array $formData)
    {
        foreach ($formData as $key => $value) {
            if ($value || $value === 0) {
                if ($key === BusinessProfileSuggestEdit::KEY_MAP) {
                    $value = BusinessProfileSuggestEdit::KEY_LABELS[$key];
                }

                $suggestEdit = new BusinessProfileSuggestEdit();
                $suggestEdit->setKey($key)
                    ->setValue($value)
                    ->setBusinessProfile($this->request->attributes->get('businessProfile'));
                $this->em->persist($suggestEdit);
            }
        }

        $this->em->flush();
    }
}
