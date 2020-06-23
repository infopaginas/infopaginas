<?php

namespace Oxa\Sonata\AdminBundle\Filter;

use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\DoctrineORMAdminBundle\Filter\StringFilter;

class CaseInsensitiveBusinessNameStringFilter extends StringFilter
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

        $data['value'] = mb_strtolower($data['value']);

        $parameterName = $this->getNewParameterName($queryBuilder);

        $this->applyWhere(
            $queryBuilder,
            sprintf('LOWER(%s.%s) = :%s', $alias, $field, $parameterName)
        );

        $queryBuilder->setParameter($parameterName, $data['value']);
    }
}
