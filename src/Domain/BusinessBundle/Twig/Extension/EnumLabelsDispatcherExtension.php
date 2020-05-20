<?php

namespace Domain\BusinessBundle\Twig\Extension;

use Domain\BusinessBundle\DBAL\Types\TaskStatusType;
use Domain\BusinessBundle\DBAL\Types\TaskType;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EnumLabelsDispatcherExtension extends AbstractExtension
{
    public function getFunctions()
    {
        return [
            new TwigFunction('dispatch_enum_value', [$this, 'dispatchEnumValue'])
        ];
    }

    public function dispatchEnumValue($enumValue)
    {
        $appEnumValues = array_merge(TaskType::getReadableValues(), TaskStatusType::getReadableValues());
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
