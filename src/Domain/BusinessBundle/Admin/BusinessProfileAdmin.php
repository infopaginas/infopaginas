<?php

namespace Domain\BusinessBundle\Admin;

use Doctrine\Common\Collections\Collection;
use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\Review\BusinessReview;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class BusinessProfileAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name')
            ->add('user')
            ->add('subscription')
            ->add('categories')
            ->add('email')
            ->add('phone')
            ->add('registrationDate', 'doctrine_orm_datetime_range', array(
                'field_type' => 'sonata_type_datetime_range_picker',
                'field_options' => array('format' => 'dd-MM-y hh:mm:ss')
            ))
            ->add('updatedAt', 'doctrine_orm_datetime_range', array(
                'field_type' => 'sonata_type_datetime_range_picker',
                'field_options' => array('format' => 'dd-MM-y hh:mm:ss')
            ))
            ->add('isActive', null, [], null, [
                'choices' => [
                    1 => 'label_yes',
                    2 => 'label_no',
                ], 
                'translation_domain' => 'AdminDomainBusinessBundle'
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
            ->add('name')
            ->add('user.username')
            ->add('subscription.name')
            ->add('categories')
            ->add('email')
            ->add('phone')
            ->add('registrationDate')
            ->add('updatedAt')
            ->add('isActive', null, ['editable' => true])
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        // define group zoning
        $formMapper
            ->tab('Profile', array('class' => 'col-md-6'))
                ->with('General', array('class' => 'col-md-4'))->end()
                ->with('Description', array('class' => 'col-md-8'))->end()
                ->with('Categories', array('class' => 'col-md-12'))->end()
            ->end()
            ->tab('Status', array('class' => 'col-md-6'))
                ->with('General', array('class' => 'col-md-6'))->end()
                ->with('Displayed blocks', array('class' => 'col-md-6'))->end()
            ->end()
            ->tab('Address', array('class' => 'col-md-6'))
            ->end()
            ->tab('Media', array('class' => 'col-md-6'))
            ->end()
            ->tab('Reviews', array('class' => 'col-md-6'))
            ->end()
            ->tab('SEO', array('class' => 'col-md-6'))
            ->end()
        ;

        $formMapper
            ->tab('Profile')
                ->with('General')
                    ->add('name')
                    ->add('user', 'sonata_type_model_list', [
                        'required' => false,
                        'btn_delete' => false,
                        'btn_add' => false,
                    ])
                    ->add('website')
                    ->add('email', 'email')
                    ->add('phone')
                    ->add('workingHours')
                    ->add('slug', null, [
                        'read_only' => true,
                        'required' => false,
                    ])
                ->end()
                ->with('Description')
                    ->add('slogan')
                    ->add('product')
                    ->add('description', 'ckeditor')
                ->end()
                ->with('Categories')
                    ->add('areas', 'sonata_type_model', [
                        'multiple' => true,
                        'required' => false,
                    ])
                    ->add('brands', 'sonata_type_model', [
                        'multiple' => true,
                        'required' => false,
                    ])
                    ->add('tags', 'sonata_type_model', [
                        'multiple' => true,
                        'required' => false,
                    ])
                    ->add('categories', 'sonata_type_model', [
                        'multiple' => true,
                        'required' => false,
                    ])

                    ->add('paymentMethods', 'sonata_type_model', [
                        'multiple' => true,
                        'expanded' => true,
                        'required' => false,
                    ])
                ->end()
            ->end()
            ->tab('Status')
                ->with('General')
                    ->add('subscription', null, [])
                    ->add('isActive')
                    ->add('updatedAt', 'sonata_type_datetime_picker', [
                        'required' => false,
                        'disabled' => true,

                    ])
                    ->add('updatedUser', 'sonata_type_model', [
                        'required' => false,
                        'btn_add' => false,
                        'disabled' => true,

                    ])
                ->end()
                ->with('Displayed blocks')
                    ->add('isSetDescription')
                    ->add('isSetMap')
                    ->add('isSetAd')
                    ->add('isSetLogo')
                    ->add('isSetSlogan')
                ->end()
            ->end()
            ->tab('Reviews')
                ->add('businessReviews', 'sonata_type_collection', array(
                    'mapped' => true,
                    'type_options' => array(
                        'delete' => true,
                        'delete_options' => array(
                            'type'         => 'checkbox',
                            'type_options' => array(
                                'mapped'   => false,
                                'required' => false,
                            )
                        )
                    )
                ), array(
                    'edit' => 'inline',
                    'inline' => 'table',
                    'sortable' => 'id',
                    'allow_delete' => true,
                ))
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
            ->add('name')
            ->add('user')
            ->add('subscription')
            ->add('categories')
            ->add('areas')
            ->add('brands')
            ->add('paymentMethods')
            ->add('tags')
            ->add('businessReviews')
            ->add('website')
            ->add('email')
            ->add('phone')
            ->add('registrationDate')
            ->add('slogan')
            ->add('description')
            ->add('product')
            ->add('workingHours')
            ->add('isSetDescription')
            ->add('isSetMap')
            ->add('isSetAd')
            ->add('isSetLogo')
            ->add('isSetSlogan')
            ->add('slug')
            ->add('updatedAt')
            ->add('updatedUser')
            ->add('isActive')
        ;
    }

    public function preUpdate($object)
    {
        $this->setReviewBusinessProfile($object);
    }

    public function prePersist($object)
    {
        $this->setReviewBusinessProfile($object);
    }

    /**
     * Used for sonata_type_collection
     *
     * @param BusinessProfile $object
     */
    private function setReviewBusinessProfile(BusinessProfile $object)
    {
        /** @var BusinessProfile $object */
        foreach($object->getBusinessReviews() as $businessReview) {
            /** @var BusinessReview $businessReview */
            if( !$businessReview->getId() ) {
                $businessReview->setBusinessProfile($object);
            }
        }
    }
}
