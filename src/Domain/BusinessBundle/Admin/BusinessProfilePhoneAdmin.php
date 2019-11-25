<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Validator\Constraints\Regex;

class BusinessProfilePhoneAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('phone')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('phone')
            ->add('type')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('type', ChoiceType::class, [
                'choices'  => BusinessProfilePhone::getTypes(),
                'multiple' => false,
                'expanded' => true,
                'required' => true,
                'translation_domain' => 'AdminDomainBusinessBundle',
            ])
            ->add('phone', null, [
                'constraints' => [
                    new Regex([
                        'pattern' => BusinessProfilePhone::REGEX_PHONE_PATTERN,
                        'message' => 'business_profile.phone.invalid',
                    ]),
                ],
                'attr' => [
                    'required' => true,
                ],
            ])
            ->add('extension')
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('phone')
        ;
    }
}
