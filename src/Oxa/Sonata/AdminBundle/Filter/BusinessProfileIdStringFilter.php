<?php

namespace Oxa\Sonata\AdminBundle\Filter;

use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\Type\Operator\ContainsOperatorType;
use Sonata\DoctrineORMAdminBundle\Filter\StringFilter;

class BusinessProfileIdStringFilter extends StringFilter
{
    /**
     * {@inheritdoc}
     */
    public function filter(ProxyQueryInterface $queryBuilder, $alias, $field, $data)
    {
        if (!$data || !is_array($data) || !array_key_exists('value', $data)) {
            return;
        }

        if (strlen($data['value']) == 0) {
            return;
        }

        $data['value'] = (int)str_replace(',', '', trim($data['value']));

        $parameterName = $this->getNewParameterName($queryBuilder);

        $this->applyWhere(
            $queryBuilder,
            sprintf('%s.%s = :%s', $alias, $field, $parameterName)
        );

        $queryBuilder->setParameter($parameterName, $data['value']);
    }
}
