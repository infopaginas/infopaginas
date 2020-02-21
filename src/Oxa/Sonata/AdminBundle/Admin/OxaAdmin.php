<?php
namespace Oxa\Sonata\AdminBundle\Admin;

use Domain\BusinessBundle\Model\DatetimePeriodStatusInterface;
use Domain\BusinessBundle\Util\Traits\StatusTrait;
use Domain\BusinessBundle\VO\VirtualObjectInterface;
use Domain\ReportBundle\Model\UserActionModel;
use Oxa\Sonata\AdminBundle\Model\ChangeStateInterface;
use Oxa\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Oxa\Sonata\AdminBundle\Model\PostponeRemoveInterface;
use Oxa\Sonata\MediaBundle\Entity\Media;
use Pix\SortableBehaviorBundle\Services\PositionHandler;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Admin\Admin as BaseAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\CoreBundle\Validator\ErrorElement;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OxaAdmin extends BaseAdmin
{
    const SONATA_URL_TYPE_LIST   = 'list';
    const SONATA_URL_TYPE_CREATE = 'create';
    const SONATA_URL_TYPE_EDIT   = 'edit';
    const SONATA_URL_TYPE_SHOW   = 'show';

    /**
     * Valid form datetime format
     */
    const FORM_DATETIME_FORMAT = 'dd.MM.yyyy, HH:mm';

    /**
     * Valid form date format
     */
    const FORM_DATE_FORMAT = 'dd.MM.yyyy';

    /**
     * Valid filter datetime format
     */
    const FILTER_DATETIME_FORMAT = 'dd-MM-y HH:mm:ss';

    /**
     * Valid filter date format
     */
    const FILTER_DATE_FORMAT = 'dd-MM-y';

    /**
     * Used to set default translations for filter boolean labels
     *
     * @var array
     */
    protected $defaultDatagridBooleanTypeOptions = [
        'choices' => [
            1 => 'label_yes',
            2 => 'label_no',
        ],
        'translation_domain' => 'SonataAdminBundle'
    ];

    /**
     * Used to set default translations for filter boolean labels
     *
     * @var array
     */
    protected $defaultDatagridStatusTypeOptions = [
        'choices' => [
            1 => 'label_yes',
            2 => 'label_no',
        ],
        'translation_domain' => 'SonataAdminBundle'
    ];

    /**
     * Used to set default datetime options
     *
     * @var array
     */
    protected $defaultDatagridDatetimeTypeOptions = [
        'field_type' => 'sonata_type_datetime_range_picker',
        'field_options' => [
            'field_options' => [
                'format' => self::FILTER_DATETIME_FORMAT
            ],
        ]
    ];

    /**
     * Used to set default datetime options
     *
     * @var array
     */
    protected $defaultDatagridDateTypeOptions = [
        'field_type' => 'sonata_type_datetime_range_picker',
        'field_options' => [
            'field_options' => [
                'format' => self::FILTER_DATE_FORMAT
            ],
        ]
    ];

    /**
     * Default values to the datagrid.
     *
     * @var array
     */
    protected $datagridValues = array(
        '_page'       => 1,
        '_per_page'   => 25,
    );

    /**
     * @var int
     */
    public $lastPosition = 1;

    /**
     * @var PositionHandler $positionService
     */
    public $positionService;

    /**
     * Allows to use such functionality in filter as: include or not include, between or not between, etc
     *
     * @var bool
     */
    public $advancedFilterMode = false;

    /**
     * show list filters dropdown
     *
     * @var bool
     */
    public $showFilters = true;

    /**
     * @var bool
     */
    public $allowBatchRestore = false;

    public function setPositionService(PositionHandler $positionHandler)
    {
        $this->positionService = $positionHandler;
        $this->lastPosition = $this->positionService->getLastPosition($this->getRoot()->getClass());
    }

    /**
     * Basic admin configuration
     */
    public function configure()
    {
        $this->setPerPageOptions([10, 25, 50, 100, 250, 500]);

        // custom delete page template
        $this->setTemplate('delete', 'OxaSonataAdminBundle:CRUD:delete_custom.html.twig');
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
            ->add('move', $this->getRouterIdParameter().'/move/{position}')
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
            ]
        ]);
    }

    /**
     * @param ErrorElement $errorElement
     * @param mixed $object
     */
    public function validate(ErrorElement $errorElement, $object)
    {
        if ($object instanceof DatetimePeriodStatusInterface && $object->getStartDate()) {
            if ($object->getStartDate()->diff($object->getEndDate())->invert) {
                $errorElement->with('endDate')
                    ->addViolation('End Date must be later than Start Date')
                    ->end()
                ;
            }
        }
    }

    /**
     * @return array
     */
    public function getFilterParameters()
    {
        $parameters = parent::getFilterParameters();

        $page = $this->getRequest()->query->get('filter')['_page'];
        $perPage = $this->getRequest()->query->get('filter')['_per_page'];

        if ($page === null) {
            $page = 1;
        }

        if ($perPage === null) {
            $perPage = $this->getMaxPerPage();
        }

        $parameters = $this->datagridValues = array_merge(
            $parameters,
            [
                '_page' => $page,
                '_per_page' => $perPage
            ]
        );

        return $parameters;
    }

    /**
     * @param $actions array
     *
     * @return array
     */
    public function configureBatchActions($actions)
    {
        if ($this->allowBatchRestore and $this->hasRoute('edit') and
            $this->hasAccess('edit') and $this->hasRoute('delete') and $this->hasAccess('delete')
        ) {
            $actions['restore'] = [
                'label'                 => 'action_restore',
                'translation_domain'    => 'SonataAdminBundle',
                'ask_confirmation'      => true,
            ];
        }

        return $actions;
    }

    /**
     * @return array
     */
    protected function getDeleteDeniedAction()
    {
        return [
            'DELETE',
        ];
    }

    /**
     * @return array
     */
    protected function getEditDeniedAction()
    {
        return [
            'EDIT',
        ];
    }

    /**
     * @return array
     */
    protected function getAllowViewOnlyAction()
    {
        return [
            'CREATE',
            'EDIT',
            'DELETE',
        ];
    }

    /**
     * @return array
     */
    protected function getDeniedAllButViewAndEditActions()
    {
        return [
            'CREATE',
            'DELETE',
        ];
    }

    /**
     * @param mixed $entity
     */
    public function preUpdate($entity)
    {
        $uow = $this->getConfigurationPool()->getContainer()->get('Doctrine')->getManager()->getUnitOfWork();
        $uow->computeChangeSets();
        $changeSet = $uow->getEntityChangeSet($entity);

        foreach ($changeSet as $fieldName => $value) {
            if ($value[0] instanceof VirtualObjectInterface && $value[1] instanceof VirtualObjectInterface) {
                $changeSet[$fieldName][0] = $value[0]->getChangeSetData();
                $changeSet[$fieldName][1] = $value[1]->getChangeSetData();
                if (strcmp($changeSet[$fieldName][0], $changeSet[$fieldName][1]) === 0) {
                    unset($changeSet[$fieldName]);
                }
            } elseif (is_object($value[0]) || is_object($value[1])) {
                $changeSet[$fieldName][0] = method_exists($value[0], 'getId') ? $value[0]->getId() : 0;
                $changeSet[$fieldName][1] = method_exists($value[1], 'getId') ? $value[1]->getId() : 0;
            }
        }

        if ($entity instanceof ChangeStateInterface) {
            $entity->setChangeState($changeSet);
        }
    }

    /**
     * @param mixed $entity
     */
    public function postUpdate($entity)
    {
        $this->handleActionLog(UserActionModel::TYPE_ACTION_UPDATE, $entity);
    }

    /**
     * @param mixed $entity
     */
    public function postPersist($entity)
    {
        $this->handleActionLog(UserActionModel::TYPE_ACTION_CREATE, $entity);
    }

    /**
     * @param mixed $entity
     */
    public function postRemove($entity)
    {
        $this->handleActionLog(UserActionModel::TYPE_ACTION_PHYSICAL_DELETE, $entity);
    }

    /**
     * @param string $action
     * @param mixed  $entity
     */
    public function handleActionLog($action, $entity = null)
    {
        $container = $this->getConfigurationPool()->getContainer();
        $actionReportManager = $container->get('domain_report.manager.user_action_report_manager');

        $data = $this->generateUserLogData($action, $entity);

        $actionReportManager->registerUserAction($action, $data);
    }

    /**
     * @param mixed  $entity
     * @param string $action
     *
     * @return array
     */
    public function generateUserLogData($action, $entity = null)
    {
        $data = [
            'entity'        => $this->getClassnameLabel(),
            'entityName'    => (string) $entity,
            'type'          => $action,
        ];

        if ($entity and $entity->getId()) {
            $data['id'] = $entity->getId();
        }

        $url = $this->getActionReportUrl($action, $entity);

        if ($url) {
            $data['url'] = $url;
        }

        if ($entity && $entity instanceof ChangeStateInterface && $entity->getChangeState()) {
            foreach ($entity->getChangeState() as $key => $changeSet) {
                $data['dataSet']['dataBefore'][$key] = $changeSet[0];
                $data['dataSet']['dataAfter'][$key]  = $changeSet[1];
            }
        }

        return $data;
    }

    /**
     * @return array
     */
    public function getCustomActions()
    {
        return [];
    }

    /**
     * @param string $action
     * @param mixed  $entity
     *
     * @return string
     */
    protected function getActionReportUrl($action, $entity = null)
    {
        $url = '';

        switch ($action) {
            case UserActionModel::TYPE_ACTION_VIEW_CREATE_PAGE:
                $url = $this->generateUrl(self::SONATA_URL_TYPE_CREATE, [], UrlGeneratorInterface::ABSOLUTE_URL);
                break;
            case UserActionModel::TYPE_ACTION_VIEW_UPDATE_PAGE:
            case UserActionModel::TYPE_ACTION_CREATE:
            case UserActionModel::TYPE_ACTION_UPDATE:
            case UserActionModel::TYPE_ACTION_RESTORE:
            case UserActionModel::TYPE_ACTION_DRAFT_REJECT:
                if ($entity and $entity->getId() and $this->hasRoute(self::SONATA_URL_TYPE_EDIT)) {
                    $url = $this->generateUrl(
                        self::SONATA_URL_TYPE_EDIT,
                        [
                            'id' => $entity->getId(),
                        ],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );
                }
                break;
            case UserActionModel::TYPE_ACTION_VIEW_SHOW_PAGE:
            case UserActionModel::TYPE_ACTION_DRAFT_APPROVE:
            case UserActionModel::TYPE_ACTION_POSTPONE_DELETE:
                if ($this->hasRoute(self::SONATA_URL_TYPE_SHOW)) {
                    $urlType = self::SONATA_URL_TYPE_SHOW;
                } elseif ($this->hasRoute(self::SONATA_URL_TYPE_EDIT)) {
                    $urlType = self::SONATA_URL_TYPE_EDIT;
                } else {
                    $urlType = '';
                }

                if ($urlType) {
                    $url = $this->generateUrl(
                        $urlType,
                        [
                            'id' => $entity->getId(),
                        ],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );
                }

                break;
            case UserActionModel::TYPE_ACTION_VIEW_LIST_PAGE:
            case UserActionModel::TYPE_ACTION_EXPORT:
                $url = $this->generateUrl(
                    self::SONATA_URL_TYPE_LIST,
                    $this->getRequest()->query->all(),
                    UrlGeneratorInterface::ABSOLUTE_URL
                );
                break;
        }

        return $url;
    }
}
