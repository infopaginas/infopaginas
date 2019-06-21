<?php

namespace Domain\BusinessBundle\Admin\CustomFields;

use Domain\BusinessBundle\Entity\CustomFields\BusinessCustomFieldRadioButtonCollection;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Validator\ErrorElement;

class BusinessCustomFieldRadioButtonItemAdmin extends OxaAdmin
{
    private $isUsede = false;

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
            ->add('title', null, [
                'label' => 'title',
            ])
            ->end()
            ->add('position', 'hidden', ['attr' => ['hidden' => true]])
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

        foreach ($deleteDiff as $item) {
            $container = $this->getConfigurationPool()->getContainer();
            $isUsed = $container->get('doctrine')->getRepository(BusinessCustomFieldRadioButtonCollection::class)
                ->findBy(['value' => $item->getId()]);

            if ($isUsed && !$this->isUsede) {
                $this->isUsede = true;
                dump($this->isUsede);

                $errorElement->with('position')
                    ->addViolation($this->getTranslator()->trans('business_custom_field_item.exist'))
                    ->end();
            }
        }
    }
}
