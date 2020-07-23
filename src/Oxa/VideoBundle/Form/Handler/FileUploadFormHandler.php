<?php

namespace Oxa\VideoBundle\Form\Handler;

use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Oxa\VideoBundle\Manager\VideoManager;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class FileUploadFormHandler
 * @package Oxa\VideoBundle\Form\Handler
 */
class FileUploadFormHandler extends BaseFormHandler
{
    /** @var FormInterface */
    private $form;

    /** @var RequestStack */
    private $requestStack;

    /** @var VideoManager */
    private $videoManager;
    
    public function __construct(FormInterface $form, RequestStack $requestStack, VideoManager $videoManager)
    {
        $this->form         = $form;
        $this->requestStack = $requestStack;
        $this->videoManager = $videoManager;
    }

    /**
     * @return bool
     */
    public function process() : bool
    {
        if ($this->requestStack->getCurrentRequest()->getMethod() == Request::METHOD_POST) {
            $this->form->handleRequest($this->requestStack->getCurrentRequest());

            if ($this->form->isValid()) {
                $file = $this->form['file']->getData();
                $this->onSuccess($file);
                return true;
            }
        }

        return false;
    }

    private function onSuccess(UploadedFile $file): void
    {
        $this->getVideoManager()->uploadLocalFile($file);
    }

    private function getVideoManager(): VideoManager
    {
        return $this->videoManager;
    }
}
