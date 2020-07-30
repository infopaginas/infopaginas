<?php

namespace Oxa\Sonata\AdminBundle\Filter;

use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\Type\Operator\ContainsOperatorType;
use Sonata\DoctrineORMAdminBundle\Filter\StringFilter;

class CaseInsensitiveStringFilter extends StringFilter
{
    /**
     * {@inheritdoc}
     */
    public function filter(ProxyQueryInterface $queryBuilder, $alias, $field, $data)
    {
        if (!$data || !is_array($data) || !array_key_exists('value', $data)) {
            return;
        }

        $data['value'] = trim($data['value']);

        if (strlen($data['value']) == 0) {
            return;
        }

        $data['type'] = !isset($data['type']) ?  ContainsOperatorType::TYPE_CONTAINS : $data['type'];
        $operator = $this->getOperator((int) $data['type']);

        if (!$operator) {
            $operator = 'LIKE';
        }

        $parameterName = $this->getNewParameterName($queryBuilder);
        $this->applyWhere(
            $queryBuilder,
            sprintf(self::buildSearchQueryWithReplacedAccents(), $alias, $field, $operator, $parameterName)
        );

        if ($data['type'] == ContainsOperatorType::TYPE_EQUAL) {
            $queryBuilder->setParameter($parameterName, AdminHelper::convertAccentedString($data['value']));
        } else {
            $queryBuilder->setParameter(
                $parameterName,
                sprintf($this->getOption('format'), AdminHelper::convertAccentedString($data['value']))
            );
        }
    }

    private function getOperator($type)
    {
        $choices = array(
            ContainsOperatorType::TYPE_CONTAINS         => 'LIKE',
            ContainsOperatorType::TYPE_NOT_CONTAINS     => 'NOT LIKE',
            ContainsOperatorType::TYPE_EQUAL            => '=',
        );

        return isset($choices[$type]) ? $choices[$type] : false;
    }

    /**
     * @return string
     */
    public static function buildSearchQueryWithReplacedAccents()
    {
        $searchString = 'lower(%s.%s)';

        $accents = AdminHelper::getAccentedChars();

        foreach ($accents as $accent => $value) {
            $searchString = sprintf('replace(%s, \'%s\', \'%s\')', $searchString, $accent, $value);
        }

        return $searchString .  ' %s :%s';
    }
}
