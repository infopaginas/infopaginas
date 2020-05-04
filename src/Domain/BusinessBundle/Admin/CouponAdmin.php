<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\Entity\Coupon;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Validator\ErrorElement;

class CouponAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('title')
            ->add('businessProfile')
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
            ->add('businessProfile')
            ->add('image', null, ['template' => 'DomainBusinessBundle:Admin:Coupon/list_image.html.twig'])
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('title')
            ->add('businessProfile')
            ->add(
                'image',
                ModelListType::class,
                [
                    'btn_delete' => null,
                    'model_manager' => $this->modelManager,
                    'class' => Media::class,
                ],
                [
                    'btn_delete'      => false,
                    'link_parameters' => [
                        'context'  => OxaMediaInterface::CONTEXT_COUPON,
                        'provider' => OxaMediaInterface::PROVIDER_IMAGE,
                    ],
                ]
            );

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
            ->add('title')
            ->add('businessProfile')
            ->add('image', null, [
                'template' => 'DomainBusinessBundle:Admin:Coupon/show_image.html.twig'
            ])
        ;
    }

    /**
     * @param ErrorElement $errorElement
     * @param mixed $object
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        /** @var Coupon $object */
            $errorElement
                ->with('image')
                ->end()
            ;
    }
}
