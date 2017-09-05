<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\BusinessBundle\Entity\BusinessProfileExtraSearch;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class BusinessProfileExtraSearchAdmin extends OxaAdmin
{
    /**
     * @return BusinessProfileExtraSearch
     */
    public function getNewInstance()
    {
        /* @var BusinessProfileExtraSearch $extraSearch */
        $extraSearch = parent::getNewInstance();
        $container = $this->getConfigurationPool()->getContainer();

        $businessId = $this->getRequest()->get('objectId', false);

        if ($businessId) {
            $business = $container
                ->get('doctrine.orm.entity_manager')
                ->getRepository(BusinessProfile::class)
                ->find($businessId)
            ;

            if ($business) {
                $categories     = $business->getCategories();
                $areas          = $business->getAreas();
                $localities     = $business->getLocalities();
                $serviceType    = $business->getServiceAreasType();
                $miles          = $business->getMilesOfMyBusiness();

                foreach ($categories as $category) {
                    $extraSearch->addCategory($category);
                }

                foreach ($areas as $area) {
                    $extraSearch->addArea($area);
                }

                foreach ($localities as $locality) {
                    $extraSearch->addLocality($locality);
                }

                $extraSearch->setServiceAreasType($serviceType);
                $extraSearch->setMilesOfMyBusiness($miles);
            }
        }

        return $extraSearch;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('categories', 'sonata_type_model_autocomplete', [
                'property' => [
                    'name',
                    'searchTextEs',
                ],
                'multiple' => true,
                'required' => true,
            ])
            ->add('areas', null, [
                'multiple' => true,
                'required' => false,
                'label' => 'Areas',
                'query_builder' => function (\Domain\BusinessBundle\Repository\AreaRepository $rep) {
                    return $rep->getAvailableAreasQb();
                },
            ])
            ->add('localities', null, [
                'multiple' => true,
                'required' => false,
                'label' => 'Localities',
                'query_builder' => function (\Domain\BusinessBundle\Repository\LocalityRepository $rep) {
                    return $rep->getAvailableLocalitiesQb();
                },
            ])
            ->add('serviceAreasType', ChoiceType::class, [
                'choices'  => BusinessProfileExtraSearch::getServiceAreasTypes(),
                'multiple' => false,
                'expanded' => true,
                'required' => true,
            ])
            ->add('milesOfMyBusiness', null, [
                'required' => false,
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
            ->add('categories')
            ->add('localities')
            ->add('serviceAreasType')
            ->add('milesOfMyBusiness')
        ;
    }
}
