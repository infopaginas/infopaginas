<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\Entity\BusinessProfileKeyword;
use Domain\BusinessBundle\Entity\BusinessProfilePhone;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Validator\Constraints\Regex;

class BusinessProfileKeywordAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('valueEn')
            ->add('ValueEs')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('valueEn')
            ->add('valueEs')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $keywordAttributes = [
            'required'  => true,
            'minLength' => BusinessProfileKeyword::KEYWORD_MIN_LENGTH,
            'maxLength' => BusinessProfileKeyword::KEYWORD_MAX_LENGTH,
        ];

        $formMapper
            ->add('valueEn', null, [
                'attr' => $keywordAttributes,
            ])
            ->add('valueEs', null, [
                'attr' => $keywordAttributes,
            ])
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('valueEn')
            ->add('valueEs')
        ;
    }
}
