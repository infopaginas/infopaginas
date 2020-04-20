<?php

namespace Domain\BusinessBundle\Admin;

use Domain\BusinessBundle\Entity\Task;
use Domain\ReportBundle\Model\UserActionModel;
use Domain\BusinessBundle\DBAL\Types\TaskStatusType;
use Domain\BusinessBundle\DBAL\Types\TaskType;
use Domain\BusinessBundle\Manager\TasksManager;
use Oxa\Sonata\AdminBundle\Admin\OxaAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;

class TaskAdmin extends OxaAdmin
{
    /**
     * @var TasksManager
     */
    protected $tasksManager;

    /**
     * Default values to the datagrid.
     *
     * @var array
     */
    protected $datagridValues = array(
        '_page'       => 1,
        '_per_page'   => 25,
        '_sort_by' => 'createdAt',
        '_sort_order' => 'DESC',
    );

    /**
     * @param Task $task
     */
    public function postUpdate($task)
    {
        $reviewer = $this->getConfigurationPool()->getContainer()->get('security.token_storage')->getToken()->getUser();

        $this->tasksManager->setReviewerForTask($task, $reviewer);

        $request = $this->getRequest()->request->all();
        $status = '';

        if (isset($request['status']) && $request['status'] == TaskStatusType::TASK_STATUS_REJECTED) {
            $this->tasksManager->reject($task);
            $status = UserActionModel::TYPE_ACTION_TASK_REJECT;
        } elseif (isset($request['status']) && $request['status'] == TaskStatusType::TASK_STATUS_CLOSED) {
            $this->tasksManager->approve($task);
            $status = UserActionModel::TYPE_ACTION_TASK_APPROVE;
        }

        if ($status) {
            $this->handleActionLog(
                $status,
                $task
            );
        }
    }

    /**
     * @param TasksManager $tasksManager
     */
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
        $formMapper->add('rejectReason', null, [
            'attr' => [
                'class' => 'vertical-resize',
            ],
        ]);

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
                'disabled' => true,
            ],
            [
                'edit' => 'inline',
                'inline' => 'table',
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

    /**
     * @param RouteCollection $collection
     */
    protected function configureRoutes(RouteCollection $collection)
    {
        $collection->remove('remove');
        $collection->remove('create');
        $collection->remove('show');
    }

    /**
     * @return array
     */
    public function getExportFormats()
    {
        return Task::getExportFormats();
    }

    /**
     * @return array
     */
    public function getExportFields()
    {
        $exportFields['ID'] = 'id';
        $exportFields['Type'] = 'type';
        $exportFields['Status'] = 'status';
        $exportFields['BusinessProfileName'] = 'businessProfile.name';
        $exportFields['BusinessProfilePhone'] = 'businessProfile.mainPhone';
        $exportFields['BusinessProfileEmail'] = 'businessProfile.email';
        $exportFields['createdDate'] = 'createdAt';
        $exportFields['Approved/RejectedBy'] = 'reviewer.fullName';

        return $exportFields;
    }
}
