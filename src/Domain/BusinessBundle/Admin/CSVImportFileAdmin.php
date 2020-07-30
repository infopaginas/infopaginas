<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\Entity\CSVImportFile;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use RuntimeException;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class CSVImportFileAdmin extends OxaAdmin
{
    /**
     * @param string $name
     * @param string $template
     */
    public function setTemplate($name, $template)
    {
        $this->getTemplateRegistry()->setTemplate('edit', 'DomainBusinessBundle:Admin:csv_import_edit.html.twig');
        $this->getTemplateRegistry()->setTemplate('show', 'DomainBusinessBundle:Admin:csv_import_show.html.twig');
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
            ->add('description')
            ->add('createdUser')
            ->add('createdAt')
            ->add('isProcessed')
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('id')
            ->add('description')
            ->add('createdUser')
            ->add('createdAt')
            ->add('isProcessed')
        ;

        $this->addGridActions($listMapper);
    }

    public function getNewInstance()
    {
        $instance = parent::getNewInstance();
        $businessProfileMappingFields = CSVImportFile::getBusinessProfileMappingFields();

        foreach ($businessProfileMappingFields as $key => $value) {
            $businessProfileMappingFields[$key] = '';
        }

        $instance->setFieldsMappingJSON(json_encode($businessProfileMappingFields));
        return $instance;
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper
            ->add('file', FileType::class, [
                'required' => true,
                'attr'     => [
                    'accept' => implode(',', AdminHelper::getFormCSVFileAccept()),
                ],
            ])
            ->add('delimiter', null, [
                'required' => false,
                'attr' => [
                    'class' => 'delimiter',
                ],
                'data' => CSVImportFile::DEFAULT_DELIMITER,
            ])
            ->add('enclosure', null, [
                'required' => false,
                'data' => CSVImportFile::DEFAULT_ENCLOSURE,
            ])
            ->add('description', TextType::class, ['required' => false])
            ->add('fieldsMappingJSON', HiddenType::class, [
                'required' => true,
                'attr' => [
                    'hidden' => true,
                    'class' => 'mapping-info',
                ],
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
            ->add('description')
            ->add('createdUser')
            ->add('createdAt')
            ->add('file', null, [
                'template' => 'DomainBusinessBundle:Admin:CSVMassImport/show_file.html.twig',
            ])
            ->add('validEntriesCount')
            ->add('invalidEntriesCount')
            ->add('invalidEntriesNumbers')
            ->add('isProcessed', null, [
                'template' => 'DomainBusinessBundle:Admin:CSVMassImport/show_is_processed.html.twig',
            ])

        ;

        if ($this->getSubject()->isProcessed()) {
            $showMapper
                ->add('Created Profiles', null, [
                    'data'     => $this->getLinkedBusinessProfilesListUrl(),
                    'template' => 'DomainBusinessBundle:Admin:CSVMassImport/show_created_profiles_link.html.twig',
                ])
            ;
        }
    }

    protected function getLinkedBusinessProfilesListUrl()
    {
        $router = $this->getConfigurationPool()->getContainer()->get('router');

        return $router->generate(
            'admin_domain_business_businessprofile_list',
            [
                'filter[_page]' => 1,
                'filter[_per_page]' => $this->getMaxPerPage(),
                'filter[csvImportFile][value]' => $this->getSubject()->getId(),
            ]
        );
    }

    public function prePersist($csvImportFile)
    {
        $this->saveFile($csvImportFile);
    }

    public function saveFile(CSVImportFile $csvImportFile)
    {
        $container = $this->getConfigurationPool()->getContainer();
        $csvImportFileManager = $container->get('domain_business.manager.csv_import_file_manager');

        $uploadedFileData = $csvImportFileManager->upload($csvImportFile);

        unlink($csvImportFile->getFile());
        if ($uploadedFileData['status']) {
            $csvImportFile->setFile($uploadedFileData['link']);
        } else {
            throw new RuntimeException('Could not upload file');
        }
    }

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection
            ->remove('export')
            ->remove('edit')
            ->remove('delete')
            ->add('show')
            ->add('create')
        ;
    }
}
