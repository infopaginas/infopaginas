<?php

namespace Oxa\VideoBundle\Admin;

use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Oxa\VideoBundle\Entity\VideoMedia;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

class VideoAdmin extends OxaAdmin
{
    public $showFilters = false;

    /**
     * @var bool
     */
    public $allowBatchRestore = true;

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $video = $this->getSubject();

        if (!$video->getId()) {
            $formMapper
                ->add('videoFile', FileType::class, [
                    'data_class'    => null,
                    'mapped'        => false,
                    'required'      => false,
                    'constraints'   => new File(AdminHelper::getFormVideoFileConstrain()),
                    'attr' => [
                        'accept' => 'video/*',
                    ],
                ])
                ->add('videoUrl', UrlType::class, [
                    'data_class' => null,
                    'mapped'     => false,
                    'required'   => false,
                ])
            ;
        }

        $formMapper
            ->add('title', TextType::class, [
                'required' => true,
                'constraints' => [
                    new Length(
                        [
                            'max' => VideoMedia::VIDEO_TITLE_MAX_LENGTH,
                        ]
                    ),
                    new NotBlank(),
                ],
            ])
            ->add('description', TextareaType::class, [
                'required'    => true,
                'constraints' => [
                    new Length(
                        [
                            'max' => VideoMedia::VIDEO_TITLE_MAX_DESCRIPTION,
                        ]
                    ),
                    new NotBlank(),
                ],
                'attr' => [
                    'class' => 'vertical-resize',
                ],
            ])
        ;

        if ($video->getId()) {
            $formMapper
                ->add('status', null, [
                    'required' => false,
                    'attr' => [
                        'disabled' => true,
                        'readonly' => true,
                    ],
                ])
                ->add('isDeleted', null, [
                    'label' => 'Scheduled for deletion',
                    'required' => false,
                    'attr' => [
                        'disabled' => true,
                        'readonly' => true,
                    ],
                ])
            ;
        }
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('status')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    public function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('poster', null, [
                'template' => 'OxaVideoBundle:Admin:list_image.html.twig',
            ])
            ->add('name')
            ->add('title')
            ->add('status')
            ->add('isDeleted')
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('name')
            ->add('title')
            ->add('description')
            ->add('status')
            ->add('isDeleted', null, [
                'label' => 'Scheduled for deletion',
            ])
            ->add('posterImage', null, [
                'template' => 'OxaVideoBundle:Admin:show_image.html.twig'
            ])
            ->add('reference', null, [
                'template' => 'OxaVideoBundle:Admin:show_reference.html.twig',
            ])
        ;
    }

    public function setTemplate($name, $template)
    {
        $this->templates['show'] = 'OxaVideoBundle:Admin:show.html.twig';
    }

    /**
     * Add additional routes
     *
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('export')
            ->add('show')
        ;
    }

    /**
     * @param VideoMedia $entity
     */
    public function prePersist($entity)
    {
        $this->preSave($entity);
    }

    /**
     * @param VideoMedia $entity
     */
    public function preUpdate($entity)
    {
        $this->preSave($entity);
        parent::preUpdate($entity);
    }

    /**
     * @param VideoMedia $entity
     */
    private function preSave($entity)
    {
        $this->setVideoValue($entity);
    }

    /**
     * @param VideoMedia $entity
     *
     * @return VideoMedia
     */
    private function setVideoValue($entity)
    {
        if ($entity->getYoutubeSupport() and !$entity->getYoutubeAction()) {
            $entity->setYoutubeAction(VideoMedia::YOUTUBE_ACTION_UPDATE);
        }

        $entity = $this->uploadVideo($entity);

        return $entity;
    }

    /**
     * @param VideoMedia $entity
     *
     * @return VideoMedia
     */
    private function uploadVideo($entity)
    {
        /** @var ContainerInterface $container */
        $container = $this->getConfigurationPool()->getContainer();

        /** @var Request $request */
        $request = Request::createFromGlobals();
        $files   = current($request->files->all());

        if (!empty($files['videoFile'])) {
            $entity = $container->get('oxa.manager.video')->addVideoLocalFile($entity, current($files));
        } else {
            $form = $this->getForm();

            if ($form->has('videoUrl') && $form->get('videoUrl')->getData()) {
                $entity = $container->get('oxa.manager.video')->addVideoFromRemoteFile(
                    $entity,
                    $form->get('videoUrl')->getData()
                );
            }
        }

        return $entity;
    }
}
