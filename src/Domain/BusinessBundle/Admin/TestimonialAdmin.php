<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\Entity\Coupon;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class TestimonialAdmin extends OxaAdmin
{
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
            ->add('description', TextareaType::class, [
                'label' => 'Text',
                'required' => true,
            ])
            ->add(
                'image',
                'sonata_type_model_list',
                [
                    'btn_delete' => null,
                ],
                [
                    'btn_delete' => false,
                    'link_parameters' => [
                        'context' => OxaMediaInterface::CONTEXT_TESTIMONIAL,
                        'provider' => OxaMediaInterface::PROVIDER_IMAGE,
                    ],
                ]
            );
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
