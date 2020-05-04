<?php

namespace Domain\BusinessBundle\Admin\Review;

use Doctrine\ORM\QueryBuilder;
use Domain\BusinessBundle\Entity\Review\BusinessReview;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\AdminBundle\Filter\DateTimeRangeFilter;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Sonata\DoctrineORMAdminBundle\Filter\ChoiceFilter;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

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
            ->add('rating', ChoiceFilter::class, [
                'field_options' => [
                    'required' => false,
                    'choices' => BusinessReview::getRatingChoices(),
                ],
                'field_type' => ChoiceType::class
            ])
            ->add('isActive', null, [], null, [
                'choices' => [
                    1 => 'label_yes',
                    2 => 'label_no',
                ],
                'translation_domain' => 'AdminDomainBusinessBundle'
            ])
            ->add('createdAt', DateTimeRangeFilter::class, $this->defaultDatagridDatetimeTypeOptions)
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
                ->add('businessProfile')
                ->add('isActive')
            ->end()
            ->with('Review')
                ->add('username')
                ->add('rating', ChoiceType::class, [
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

        // remove this field if this page used as sonata_type_collection on other pages
        if ($this->getRoot()->getClass() != $this->getClass()) {
            $formMapper->remove('businessProfile');
        }
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
     *
     * @return null
     */
    public function validate(ErrorElement $errorElement, $object)
    {
            $errorElement
                ->with('rating')
                ->end()
            ;
    }

    /**
     * Modify list results
     *
     * @param string $context
     * @return \Sonata\AdminBundle\Datagrid\ProxyQueryInterface
     */
    public function createQuery($context = 'list')
    {
        /** @var QueryBuilder $query */
        $query = parent::createQuery($context);
        $query->leftJoin($query->getRootAliases()[0] . '.businessProfile', 'bp');
        return $query;
    }
}
