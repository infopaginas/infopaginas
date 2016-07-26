<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 25.07.16
 * Time: 14:57
 */

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
