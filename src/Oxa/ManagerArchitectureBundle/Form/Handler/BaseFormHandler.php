<?php

namespace Oxa\ManagerArchitectureBundle\Form\Handler;

use Oxa\ManagerArchitectureBundle\Model\Interfaces\FormHandlerInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;

/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 25.07.16
 * Time: 14:58
 */
abstract class BaseFormHandler implements FormHandlerInterface
{
    /** @var FormInterface  */
    protected $form;

    /**
     * Override this method in each form handler.
     * "Main" handler actions should be here
     * @return mixed
     */
    abstract public function process();

    /**
     * Build form errors array.
     * Use field names as array keys
     *
     * @access public
     * @param FormInterface|null $form
     * @return array
     */
    public function getErrors(FormInterface $form = null) : array
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
}
