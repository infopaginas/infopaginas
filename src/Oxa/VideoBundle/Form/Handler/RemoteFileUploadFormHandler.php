<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 19.07.16
 * Time: 22:06
 */

namespace Oxa\VideoBundle\Form\Handler;

use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Oxa\ManagerArchitectureBundle\Model\Interfaces\FormHandlerInterface;
use Oxa\VideoBundle\Manager\VideoManager;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RemoteFileUploadFormHandler
 * @package Oxa\VideoBundle\Form\Handler
 */
class RemoteFileUploadFormHandler extends BaseFormHandler implements FormHandlerInterface
{
    /** @var FormInterface */
    private $form;

    /** @var Request */
    private $request;

    /** @var VideoManager */
    private $videoManager;

    /**
     * RemoteFileUploadFormHandler constructor.
     * @param FormInterface $form
     * @param Request $request
     * @param VideoManager $videoManager
     */
    public function __construct(FormInterface $form, Request $request, VideoManager $videoManager)
    {
        $this->form          = $form;
        $this->request       = $request;
        $this->videoManager = $videoManager;
    }

    /**
     * @return bool
     */
    public function process() : bool
    {
        if ($this->request->getMethod() == 'POST') {
            $this->form->handleRequest($this->request);

            if ($this->form->isValid()) {
                $url = $this->form['url']->getData();
                $this->onSuccess($url);
                return true;
            }
        }

        return false;
    }

    /**
     * @param string $url
     */
    private function onSuccess(string $url)
    {
        $this->getVideoManager()->uploadRemoteFile($url);
    }

    /**
     * @return VideoManager
     */
    private function getVideoManager() : VideoManager
    {
        return $this->videoManager;
    }
}
