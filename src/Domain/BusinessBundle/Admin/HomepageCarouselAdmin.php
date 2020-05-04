<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\Entity\BusinessProfile;
use Domain\ReportBundle\Model\BusinessOverviewModel;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Oxa\Sonata\MediaBundle\Model\OxaMediaInterface;
use Oxa\VideoBundle\Entity\VideoMedia;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Form\Type\ModelListType;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Validator\Constraints\NotBlank;

class HomepageCarouselAdmin extends OxaAdmin
{
    protected $datagridValues = [
        '_page' => 1,
        '_per_page'   => 10,
        '_sort_order' => 'ASC',
        '_sort_by' => 'position',
    ];

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('businessProfile.name', null, [
                'show_filter' => true,
            ])
            ->add('position')
        ;
    }

    /**
     * Show all available actions for a record
     *
     * @param ListMapper $listMapper
     */
    protected function addGridActions(ListMapper $listMapper)
    {
        $listMapper->add('_action', 'actions', [
            'actions' => [
                'all_available' => [
                    'template' => 'OxaSonataAdminBundle:CRUD:list__action_delete_physical_able.html.twig'
                ],
                'move'    => [
                    'template' => 'OxaSonataAdminBundle:CRUD:list_sorting.html.twig'
                ],
            ],
        ]);
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('businessProfile')
            ->add('position')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('businessProfile', ModelListType::class, [
                'required'   => true,
                'btn_add'    => false,
                'btn_delete' => false,
                'model_manager' => $this->modelManager,
                'class' => BusinessProfile::class,
            ])
            ->add('position')
            ->add(
                'image',
                ModelListType::class,
                [
                    'constraints' => [new NotBlank()],
                    'sonata_help' => 'homepageCarouselImageRatioHelpMessage',
                    'model_manager' => $this->modelManager,
                    'class' => Media::class,
                ],
                [
                    'link_parameters' => [
                        'context'  => OxaMediaInterface::CONTEXT_HOMEPAGE_CAROUSEL,
                        'provider' => OxaMediaInterface::PROVIDER_IMAGE,
                    ],
                ]
            )
            ->add('video', ModelListType::class, [
                'required' => false,
                'model_manager' => $this->modelManager,
                'class' => VideoMedia::class,
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
            ->add('businessProfile')
            ->add('position')
            ->add('image', null, ['template' => 'DomainArticleBundle:Admin:show_image.html.twig'])
            ->add('video.posterImage', null, [
                'template' => 'OxaVideoBundle:Admin:show_business_video_image.html.twig'
            ])
            ->add('video.reference', null, [
                'template' => 'OxaVideoBundle:Admin:show_business_video_reference.html.twig',
            ])
            ->add('video', null, [
                'label'     => 'Video',
                'eventType'     => BusinessOverviewModel::TYPE_CODE_VIDEO_WATCHED,
                'template' => 'DomainBusinessBundle:Admin:BusinessProfile/report_data.html.twig',
            ])
        ;
    }
}
