<?php
namespace Oxa\Sonata\AdminBundle\Admin;

use Domain\BusinessBundle\Model\DatetimePeriodStatusInterface;
use Domain\BusinessBundle\Util\Traits\StatusTrait;
use Oxa\Sonata\AdminBundle\Model\CopyableEntityInterface;
use Pix\SortableBehaviorBundle\Services\PositionHandler;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Admin\Admin as BaseAdmin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

class OxaAdmin extends BaseAdmin
{
    /**
     * Valid form datetime format
     */
    const FORM_DATETIME_FORMAT = 'dd.MM.yyyy, HH:mm';

    /**
     * Valid filter datetime format
     */
    const FILTER_DATETIME_FORMAT = 'dd-MM-y hh:mm:ss';

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
            'format' => self::FILTER_DATETIME_FORMAT
        ]
    ];

    /**
     * Default values to the datagrid.
     *
     * @var array
     */
    protected $datagridValues = array(
        '_page'       => 1,
        '_per_page'   => 10,
        '_sort_by' => 'position',
    );

    /**
     * @var int
     */
    public $lastPosition = 1;

    /**
     * @var PositionHandler $positionService
     */
    public $positionService;

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
        $this->setTemplate('delete', 'OxaSonataAdminBundle:CRUD:delete.html.twig');
    }

    /**
     * Add additional actions
     *
     * @return array
     */
    public function getBatchActions()
    {
        $actions = parent::getBatchActions();

        // delete from database action
        if ($this->isGranted('ROLE_PHYSICAL_DELETE_ABLE') && $this->hasRoute('delete_physical')) {
            $actions['delete_physical'] = [
                'label' => $this->trans('action_delete_physical'),
                'ask_confirmation' => true
            ];
        }

        // restore deleted record
        if ($this->isGranted('ROLE_RESTORE_ABLE') && $this->hasRoute('restore')) {
            $actions['restore'] = [
                'label' => $this->trans('action_restore'),
                'ask_confirmation' => false
            ];
        }

        return $actions;
    }

    /**
     * Configure record list
     *
     * @return \Sonata\AdminBundle\Datagrid\DatagridInterface
     */
    public function getDatagrid()
    {
        // Display deleted records as well in the list
        if ($this->isGranted('ROLE_PHYSICAL_DELETE_ABLE')) {
            /* @var \Gedmo\SoftDeleteable\Filter\SoftDeleteableFilter $softDeleteableFilter */
            $softDeleteableFilter = $this->getConfigurationPool()
                ->getContainer()
                ->get('doctrine.orm.default_entity_manager')
                ->getFilters()
                ->getFilter('softdeleteable');

            $softDeleteableFilter->disableForEntity($this->getClass());
        }

        return parent::getDatagrid();
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
            ->add('delete_physical', null, [
                '_controller' => 'OxaSonataAdminBundle:CRUD:deletePhysical'
            ])
            ->add('restore')
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
}
