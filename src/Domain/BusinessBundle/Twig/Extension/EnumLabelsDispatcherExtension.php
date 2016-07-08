<?php

namespace Domain\BusinessBundle\Twig\Extension;

use Domain\BusinessBundle\DBAL\Types\TaskStatusType;
use Domain\BusinessBundle\DBAL\Types\TaskType;

class EnumLabelsDispatcherExtension extends \Twig_Extension
{
    public function getFunctions()
    {
        return ['dispatch_enum_value' => new \Twig_Function_Method($this, 'dispatchEnumValue')];
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
