<?php

namespace Domain\BusinessBundle\Admin;

use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class CSVImportFileAdmin extends OxaAdmin
{
    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $datagridMapper
            ->add('id')
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
            ->add('createdUser')
            ->add('createdAt')
            ->add('isProcessed')
        ;

        $this->addGridActions($listMapper);
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
            ->add('delimiter', null, ['required' => false])
            ->add('enclosure', null, ['required' => false])
        ;
    }

    /**
     * @param ShowMapper $showMapper
     */
    protected function configureShowFields(ShowMapper $showMapper)
    {
        $showMapper
            ->add('id')
            ->add('createdUser')
            ->add('createdAt')
            ->add('file', null, [
                'template' => 'DomainBusinessBundle:Admin:CSVMassImport/show_file.html.twig',
            ])
            ->add('isProcessed')
        ;
    }

    public function prePersist($csvImportFile)
    {
        $this->saveFile($csvImportFile);
    }

    public function saveFile($csvImportFile)
    {
        $container = $this->getConfigurationPool()->getContainer();
        $csvImportFileManager = $container->get('domain_business.manager.csv_import_file_manager');

        $uploadedFileData = $csvImportFileManager->upload($csvImportFile);

        unlink($csvImportFile->getFile());
        if ($uploadedFileData['status']) {
            $csvImportFile->setFile($uploadedFileData['link']);
        } else {
            throw new \RuntimeException();
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
