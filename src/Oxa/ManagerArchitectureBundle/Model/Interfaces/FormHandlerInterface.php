<?php

namespace Oxa\ManagerArchitectureBundle\Model\Interfaces;

use Symfony\Component\Form\FormInterface;

/**
 * Interface FormHandlerInterface
 * @package Oxa\ManagerArchitectureBundle\Model\Interfaces
 */
interface FormHandlerInterface
{
    /**
     * @return mixed
     */
    public function process();

    /**
     * @param FormInterface|null $form
     * @return array
     */
    public function getErrors(FormInterface $form = null) : array;
}
