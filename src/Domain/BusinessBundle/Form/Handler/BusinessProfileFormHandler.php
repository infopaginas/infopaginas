<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 29.06.16
 * Time: 19:48
 */

namespace Domain\BusinessBundle\Form\Handler;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class BusinessProfileFormHandler
 * @package Domain\BusinessBundle\Form\Handler
 */
class BusinessProfileFormHandler
{
    /** @var FormInterface  */
    protected $form;

    /** @var Request  */
    protected $request;

    /**
     * BusinessProfileFormHandler constructor.
     * @param FormInterface $form
     * @param Request $request
     */
    public function __construct(
        FormInterface $form,
        Request $request
    ) {
        $this->form           = $form;
        $this->request        = $request;
    }

    public function process()
    {

    }

    public function getErrors()
    {

    }
}