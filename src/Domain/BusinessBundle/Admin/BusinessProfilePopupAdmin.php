<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\Entity\BusinessProfilePopup;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Exception\RuntimeException;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;

class BusinessProfilePopupAdmin extends OxaAdmin
{
    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('file')
            ->add('message')
        ;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        /** @var BusinessProfilePopup $popup */
        $popup = $this->getSubject();
        $fileURL = $popup->getFile();

        $formMapper
            ->add('file', FileType::class, [
                'label' => 'Attachment',
                'data_class' => null,
                'constraints' => new File(AdminHelper::getFormPopupFileConstrain()),
                'attr' => [
                    'accept' => BusinessProfilePopup::FILE_MIME_TYPE,
                ],
                'help' => $fileURL ? '<a href="' . $fileURL . '" target="_blank">Attached file</a>' : '',
                'required' => true,
            ])
            ->add('message', null, [
                'required' => true,
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
            ->add('message')
        ;
    }

    /**
     * @param BusinessProfilePopup $entity
     */
    public function prePersist($entity)
    {
        $this->uploadPopupAttachment($entity);
    }

    /**
     * @param BusinessProfilePopup $entity
     */
    public function preUpdate($entity)
    {
        $this->uploadPopupAttachment($entity);
        parent::preUpdate($entity);
    }

    private function uploadPopupAttachment(BusinessProfilePopup $popup): void
    {
        $container = $this->getConfigurationPool()->getContainer();
        $businessProfilePopupManager = $container->get('domain_business.manager.business_profile_popup');
        $uploadedFileData = $businessProfilePopupManager->upload($popup);

        unlink($popup->getFile());
        if ($uploadedFileData['status']) {
            $popup->setFile($uploadedFileData['link']);
        } else {
            throw new RuntimeException('Could not upload file');
        }
    }
}
