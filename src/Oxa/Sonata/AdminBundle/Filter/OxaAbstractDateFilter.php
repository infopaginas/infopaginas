<?php

namespace Oxa\Sonata\AdminBundle\Filter;

use Sonata\DoctrineORMAdminBundle\Filter\AbstractDateFilter;
use Sonata\AdminBundle\Form\Type\Filter\DateType;
use Sonata\AdminBundle\Form\Type\Filter\DateRangeType;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;

abstract class OxaAbstractDateFilter extends AbstractDateFilter
{
    /**
     * {@inheritdoc}
     */
    public function filter(ProxyQueryInterface $queryBuilder, $alias, $field, $data)
    {
        // check data sanity
        if (!$data || !is_array($data) || !array_key_exists('value', $data)) {
            return;
        }

        if ($this->range) {
            // additional data check for ranged items
            if (!array_key_exists('start', $data['value']) || !array_key_exists('end', $data['value'])) {
                return;
            }

            if (!$data['value']['start'] && !$data['value']['end']) {
                return;
            }

            // date filter should filter records for the whole days
            if ($this->time === false) {
                if ($data['value']['start'] instanceof \DateTime) {
                    $data['value']['start']->setTime(0, 0, 0);
                }

                if ($data['value']['end'] instanceof \DateTime) {
                    $data['value']['end']->setTime(23, 59, 59);
                }
            }

            // transform types
            if ($this->getOption('input_type') === 'timestamp') {
                $data['value']['start'] = $data['value']['start'] instanceof \DateTime
                    ? $data['value']['start']->getTimestamp()
                    : 0;
                $data['value']['end'] = $data['value']['end'] instanceof \DateTime
                    ? $data['value']['end']->getTimestamp()
                    : 0;
            }

            // default type for range filter
            $data['type'] = !isset($data['type']) || !is_numeric($data['type'])
                ? DateRangeType::TYPE_BETWEEN
                : $data['type'];
            $startDateParameterName = $this->getNewParameterName($queryBuilder);
            $endDateParameterName = $this->getNewParameterName($queryBuilder);

            if ($data['type'] == DateRangeType::TYPE_NOT_BETWEEN) {
                $this->applyWhere(
                    $queryBuilder,
                    sprintf(
                        '%s.%s < :%s OR %s.%s > :%s',
                        $alias,
                        $field,
                        $startDateParameterName,
                        $alias,
                        $field,
                        $endDateParameterName
                    )
                );
            } else {
                if ($data['value']['start']) {
                    $this->applyWhere(
                        $queryBuilder,
                        sprintf('%s.%s %s :%s', $alias, $field, '>=', $startDateParameterName)
                    );
                }

                if ($data['value']['end']) {
                    $this->applyWhere(
                        $queryBuilder,
                        sprintf('%s.%s %s :%s', $alias, $field, '<=', $endDateParameterName)
                    );
                }
            }

            if ($data['value']['start']) {
                $queryBuilder->setParameter($startDateParameterName, $data['value']['start']);
            }

            if ($data['value']['end']) {
                $queryBuilder->setParameter($endDateParameterName, $data['value']['end']);
            }
        } else {
            if (!$data['value']) {
                return;
            }

            // default type for simple filter
            $data['type'] = !isset($data['type']) || !is_numeric($data['type']) ? DateType::TYPE_EQUAL : $data['type'];
            // just find an operator and apply query
            $operator = $this->getOperator($data['type']);

            // transform types
            if ($this->getOption('input_type') === 'timestamp') {
                $data['value'] = $data['value'] instanceof \DateTime ? $data['value']->getTimestamp() : 0;
            }

            // null / not null only check for col
            if (in_array($operator, array('NULL', 'NOT NULL'))) {
                $this->applyWhere($queryBuilder, sprintf('%s.%s IS %s ', $alias, $field, $operator));

                return;
            }

            $parameterName = $this->getNewParameterName($queryBuilder);

            // date filter should filter records for the whole day
            if ($this->time === false && $data['type'] == DateType::TYPE_EQUAL) {
                $this->applyWhere($queryBuilder, sprintf('%s.%s %s :%s', $alias, $field, '>=', $parameterName));
                $queryBuilder->setParameter($parameterName, $data['value']);
                $endDateParameterName = $this->getNewParameterName($queryBuilder);
                $this->applyWhere($queryBuilder, sprintf('%s.%s %s :%s', $alias, $field, '<', $endDateParameterName));

                if ($this->getOption('input_type') === 'timestamp') {
                    $endValue = strtotime('+1 day', $data['value']);
                } else {
                    $endValue = clone $data['value'];
                    $endValue->add(new \DateInterval('P1D'));
                }

                $queryBuilder->setParameter($endDateParameterName, $endValue);

                return;
            }

            $this->applyWhere($queryBuilder, sprintf('%s.%s %s :%s', $alias, $field, $operator, $parameterName));
            $queryBuilder->setParameter($parameterName, $data['value']);
        }
    }
}
