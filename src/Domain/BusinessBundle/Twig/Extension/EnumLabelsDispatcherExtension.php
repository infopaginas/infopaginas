<?php

namespace Domain\BusinessBundle\Twig\Extension;

use Domain\BusinessBundle\DBAL\Types\TaskStatusType;
use Domain\BusinessBundle\DBAL\Types\TaskType;
use Twig\TwigFunction;

class EnumLabelsDispatcherExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return ['dispatch_enum_value' => new TwigFunction($this, 'dispatchEnumValue')];
    }

    public function dispatchEnumValue($enumValue)
    {
        $appEnumValues = array_merge(TaskType::getChoices(), TaskStatusType::getChoices());
        return $appEnumValues[$enumValue] ?? '';
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'enum_labels_dispatcher_extension';
    }
}
