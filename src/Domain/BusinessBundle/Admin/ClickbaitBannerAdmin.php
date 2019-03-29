<?php

namespace Domain\BusinessBundle\Admin;

use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Validator\Constraints\NotBlank;

class ClickbaitBannerAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('locality')
            ->add('isActive')
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
            ->add('locality', null, [
                'template' => 'DomainBusinessBundle:Admin:ClickbaitBanner/list_locality.html.twig',
            ])
            ->add('isActive')
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
            ->tab('Clickbait Banner')
                ->with('Locality')
                    ->add('locality', null, ['required' => true])
                    ->add('isActive')
                    ->add('title')
                    ->add('url', null, ['required' => true])
                    ->add(
                        'image',
                        'sonata_type_model_list',
                        ['constraints' => [new NotBlank()]],
                        [
                            'link_parameters' => [
                                'context'  => OxaMediaInterface::CONTEXT_CLICKBAIT_BANNER,
                                'provider' => OxaMediaInterface::PROVIDER_IMAGE,
                            ]
                        ]
                    )
                ->end()
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
            ->add('locality')
            ->add('isActive')
            ->add('title')
            ->add('url')
            ->add('image', null, ['template' => 'DomainArticleBundle:Admin:show_image.html.twig'])
        ;
    }
}
