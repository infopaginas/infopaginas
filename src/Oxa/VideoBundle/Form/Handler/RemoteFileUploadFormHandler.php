<?php

namespace Oxa\VideoBundle\Form\Handler;

use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Oxa\VideoBundle\Manager\VideoManager;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class RemoteFileUploadFormHandler
 * @package Oxa\VideoBundle\Form\Handler
 */
class RemoteFileUploadFormHandler extends BaseFormHandler
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

    public function process(): bool
    {
        if ($this->requestStack->getCurrentRequest()->getMethod() == Request::METHOD_POST) {
            $this->form->handleRequest($this->requestStack->getCurrentRequest());

            if ($this->form->isValid()) {
                $url = $this->form['url']->getData();
                $this->onSuccess($url);
                return true;
            }
        }

        return false;
    }

    private function onSuccess(string $url): void
    {
        $this->getVideoManager()->uploadRemoteFile($url);
    }

    private function getVideoManager(): VideoManager
    {
        return $this->videoManager;
    }
}
