<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 19.07.16
 * Time: 22:06
 */

namespace Oxa\WistiaBundle\Form\Handler;

use Oxa\ManagerArchitectureBundle\Form\Handler\BaseFormHandler;
use Oxa\ManagerArchitectureBundle\Model\Interfaces\FormHandlerInterface;
use Oxa\WistiaBundle\Manager\WistiaManager;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class RemoteFileUploadFormHandler
 * @package Oxa\WistiaBundle\Form\Handler
 */
class RemoteFileUploadFormHandler extends BaseFormHandler implements FormHandlerInterface
{
    /** @var FormInterface */
    private $form;

    /** @var Request */
    private $request;

    /** @var WistiaManager */
    private $wistiaManager;

    /**
     * RemoteFileUploadFormHandler constructor.
     * @param FormInterface $form
     * @param Request $request
     * @param WistiaManager $wistiaManager
     */
    public function __construct(FormInterface $form, Request $request, WistiaManager $wistiaManager)
    {
        $this->form          = $form;
        $this->request       = $request;
        $this->wistiaManager = $wistiaManager;
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
        $this->getWistiaManager()->uploadRemoteFile($url);
    }

    /**
     * @return WistiaManager
     */
    private function getWistiaManager() : WistiaManager
    {
        return $this->wistiaManager;
    }
}
