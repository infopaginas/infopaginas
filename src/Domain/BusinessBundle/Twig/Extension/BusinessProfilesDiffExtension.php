<?php
/**
 * Created by PhpStorm.
 * User: Alexander Polevoy <xedinaska@gmail.com>
 * Date: 08.07.16
 * Time: 12:05
 */

namespace Domain\BusinessBundle\Twig\Extension;

use Domain\BusinessBundle\Util\BusinessProfile\BusinessProfilesComparator;
use Symfony\Component\Form\FormInterface;

class BusinessProfilesDiffExtension extends \Twig_Extension
{
    /** @var FormInterface */
    private $form;

    public function setFormObject(FormInterface $form)
    {
        $this->form = $form;
    }

    public function getFunctions()
    {
        return ['get_business_profiles_diff_array' => new \Twig_Function_Method($this, 'getBusinessProfilesDiffArray')];
    }

    public function getBusinessProfilesDiffArray($updatedBusinessProfile, $currentBusinessProfile)
    {
        $updatedBusinessProfileForm = $this->form;
        $updatedBusinessProfileForm->setData($updatedBusinessProfile);

        $currentBusinessProfileForm = clone $updatedBusinessProfileForm;
        $currentBusinessProfileForm->setData($currentBusinessProfile);

        return BusinessProfilesComparator::compare($updatedBusinessProfileForm, $currentBusinessProfileForm);
    }

    /**
     * @return string
     */
    public function getName() : string
    {
        return 'business_profiles_diff_extension';
    }
}
