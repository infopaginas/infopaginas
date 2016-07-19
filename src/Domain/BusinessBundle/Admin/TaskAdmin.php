<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\DBAL\Types\TaskStatusType;
use Domain\BusinessBundle\DBAL\Types\TaskType;
use Domain\BusinessBundle\Manager\TasksManager;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Symfony\Component\HttpFoundation\Request;

class TaskAdmin extends OxaAdmin
{
    /**
     * @var TasksManager
     */
    protected $tasksManager;

    public function postUpdate($task)
    {
        $reviewer = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();

        $this->tasksManager->setReviewerForTask($task, $reviewer);

        $request = $this->getRequest()->request->all();

        if (isset($request['status']) && $request['status'] == TaskStatusType::TASK_STATUS_REJECTED) {
            $this->tasksManager->reject($task);
        } elseif (isset($request['status']) && $request['status'] == TaskStatusType::TASK_STATUS_CLOSED) {
            $this->tasksManager->approve($task);
        }
    }

    public function setTasksManager(TasksManager $tasksManager)
    {
        $this->tasksManager = $tasksManager;
    }

    /**
     * @param DatagridMapper $datagridMapper
     */
    protected function configureDatagridFilters(DatagridMapper $datagridMapper)
    {
        $typeFieldOptions = [
            'choices'     =>  TaskType::getChoices(),
            'placeholder' => $this->trans('All'),
        ];

        $statusFieldOptions = [
            'choices'     =>  TaskStatusType::getChoices(),
            'placeholder' => $this->trans('All'),
        ];

        $datagridMapper
            ->add('type', 'doctrine_orm_choice', [], 'choice', $typeFieldOptions)
            ->add('status', 'doctrine_orm_choice', [], 'choice', $statusFieldOptions)
        ;
    }

    /**
     * @param ListMapper $listMapper
     */
    protected function configureListFields(ListMapper $listMapper)
    {
        $listMapper
            ->add('type', 'string', ['template' => 'DomainBusinessBundle:TaskAdmin:fields/type_field.html.twig',])
            ->add('createdAt', 'datetime', ['label' => $this->trans('Date'),])
            ->add('status', 'string', ['template' => 'DomainBusinessBundle:TaskAdmin:fields/status_field.html.twig'])
            ->add('reviewer', '', ['label' => $this->trans('Approved/Rejected By')])
        ;

        $this->addGridActions($listMapper);
    }

    /**
     * @param FormMapper $formMapper
     */
    protected function configureFormFields(FormMapper $formMapper)
    {
        $formMapper->add('rejectReason');

        $formMapper->add(
            'businessProfile.businessReviews',
            'sonata_type_collection',
            [
                'by_reference' => true,
                'label' => 'Business Reviews',
                'type_options' => [
                    'delete' => false
                ],
                'cascade_validation' => true,
                'required' => false,
                'btn_add' => false,
            ],
            [
                'edit' => 'inline',
                'inline' => 'table',
                'sortable' => 'position',
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
            ->add('type')
            ->add('status')
            ->add('rejectReason')
            ->add('createdAt')
            ->add('modifiedAt')
        ;
    }

    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('remove');
        $collection->remove('export');
        $collection->remove('create');
        $collection->remove('show');
    }
}