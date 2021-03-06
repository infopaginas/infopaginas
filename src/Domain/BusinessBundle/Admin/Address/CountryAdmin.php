<?php

namespace Domain\BusinessBundle\Admin\Address;

use Domain\BusinessBundle\Entity\Address\Country;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class CountryAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('name', null, [
                'show_filter' => true,
            ])
            ->add('shortName')
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
            ->add('shortName')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $country = $this->getSubject();

        $disableShortNameEdit = in_array(strtolower($country->getShortName()), Country::getRequiredCountries());

        $formMapper
            ->add('name')
            ->add(
                'shortName',
                null,
                [
                    'attr' => [
                        'read_only' => $disableShortNameEdit,
                    ],
                ]
            )
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
            ->add('shortName')
        ;
    }

    /**
     * @param string $name
     * @param null $object
     *
     * @return bool
     */
    public function isGranted($name, $object = null)
    {
        $deniedActions = $this->getDeleteDeniedAction();

        if ($object && in_array($name, $deniedActions) &&
            in_array(strtolower($object->getShortName()), $object::getRequiredCountries())
        ) {
            return false;
        }

        return parent::isGranted($name, $object);
    }
}
