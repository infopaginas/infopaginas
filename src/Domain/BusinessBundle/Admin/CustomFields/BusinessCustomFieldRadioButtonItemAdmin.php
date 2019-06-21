<?php

namespace Domain\BusinessBundle\Admin\CustomFields;

use Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldRadioButtonCollection;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\AdminBundle\Util\Traits\ValidateIsUsedCollectionTrait;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;

class BusinessCustomFieldRadioButtonItemAdmin extends OxaAdmin
{
    use ValidateIsUsedCollectionTrait;

    /**
     * Default values to the datagrid.
     *
     * @var array
     */
    protected $datagridValues = [
        '_page'     => 1,
        '_per_page' => 25,
        '_sort_by'  => 'position',
    ];

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('title')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('title')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->with('Item', ['class' => 'col-md-6',])
            ->add('title', null, ['label' => 'title'])
            ->end()
            ->add('position', HiddenType::class, ['attr' => ['hidden' => true]])
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('title')
        ;
    }

    public function validate(ErrorElement $errorElement, $object)
    {
        $deleteDiff = $object->getBusinessCustomFieldRadioButton()->getRadioButtonItems()->getDeleteDiff();
        $repository = $this->getConfigurationPool()->getContainer()->get('doctrine')
            ->getRepository(BusinessCustomFieldRadioButtonCollection::class);

        $this->validateIsUsedCollection($deleteDiff, $errorElement, $repository);
    }
}
