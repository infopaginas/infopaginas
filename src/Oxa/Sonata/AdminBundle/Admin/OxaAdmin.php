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
use Sonata\CoreBundle\Validator\ErrorElement;

class OxaAdmin extends BaseAdmin
{
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

    protected function getDeleteDeniedAction()
    {
        return [
            'DELETE',
        ];
    }

    protected function getAllowViewOnlyAction()
    {
        return [
            'CREATE',
            'EDIT',
            'DELETE',
        ];
    }
}
