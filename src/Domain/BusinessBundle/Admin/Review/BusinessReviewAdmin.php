<?php

namespace Domain\BusinessBundle\Admin\Review;

use Domain\BusinessBundle\Entity\Review\BusinessReview;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\ChoiceList\ChoiceList;

class BusinessReviewAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('user')
            ->add('businessProfile')
            ->add('username')
            ->add('rating', 'doctrine_orm_choice', [
                'field_options' => [
                    'required' => false,
                    'choices' => BusinessReview::getRatingChoices(),
                ],
                'field_type' => 'choice'
            ])
            ->add('isActive', null, [], null, [
                'choices' => [
                    1 => 'label_yes',
                    2 => 'label_no',
                ],
                'translation_domain' => 'AdminDomainBusinessBundle'
            ])
            ->add('createdAt', 'doctrine_orm_datetime_range', [
                'field_type' => 'sonata_type_datetime_range_picker',
                'field_options' => [
                    'format' => 'dd-MM-y hh:mm:ss'
                ]
            ])
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('user')
            ->add('businessProfile')
            ->add('username')
            ->add('rating')
            ->add('isActive')
            ->add('createdAt')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('General', array('class' => 'col-md-4'))->end()
            ->with('Review', array('class' => 'col-md-8'))->end()
        ;

        $formMapper
            ->with('General')
                ->add('user')
                ->add('businessProfile', null, [
                    // hide this field if this page used as sonata_type_collection on other pages
                    'attr' => ['hidden' => $this->getRoot()->getClass() != $this->getClass() ]
                ])
                ->add('isActive')
            ->end()
            ->with('Review')
                ->add('username')
                ->add('rating', 'choice', [
                    'choices' => BusinessReview::getRatingChoices(),
                ])
                ->add('content', null, [
                    'attr' => [
                        'rows' => 3,
                        'cols' => 100,
                        'style' => 'resize: none'
                    ]
                ])
            ->end()
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('username')
            ->add('rating')
            ->add('content')
            ->add('isActive')
        ;
    }

    /**
     * @param ErrorElement $errorElement
     * @param mixed $object
     * @return null
     */
    public function validate(ErrorElement $errorElement, $object)
    {
            $errorElement
                ->with('rating')
                ->end()
            ;
    }
}
