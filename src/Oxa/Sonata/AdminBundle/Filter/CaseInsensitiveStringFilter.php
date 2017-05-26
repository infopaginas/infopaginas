<?php

namespace Oxa\Sonata\AdminBundle\Filter;

use Oxa\Sonata\AdminBundle\Util\Helpers\AdminHelper;
use Sonata\AdminBundle\Form\Type\Filter\ChoiceType;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
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

        $data['type'] = !isset($data['type']) ?  ChoiceType::TYPE_CONTAINS : $data['type'];
        $operator = $this->getOperator((int) $data['type']);

        if (!$operator) {
            $operator = 'LIKE';
        }

        $parameterName = $this->getNewParameterName($queryBuilder);
        $this->applyWhere(
            $queryBuilder,
            sprintf($this->buildSearchQueryWithReplacedAccents(), $alias, $field, $operator, $parameterName)
        );

        if ($data['type'] == ChoiceType::TYPE_EQUAL) {
            $queryBuilder->setParameter($parameterName, $this->convertSearchValue($data['value']));
        } else {
            $queryBuilder->setParameter(
                $parameterName,
                sprintf($this->getOption('format'), $this->convertSearchValue($data['value']))
            );
        }
    }

    private function getOperator($type)
    {
        $choices = array(
            ChoiceType::TYPE_CONTAINS         => 'LIKE',
            ChoiceType::TYPE_NOT_CONTAINS     => 'NOT LIKE',
            ChoiceType::TYPE_EQUAL            => '=',
        );

        return isset($choices[$type]) ? $choices[$type] : false;
    }

    /**
     * @param $value string
     *
     * @return string
     */
    private function convertSearchValue($value)
    {
        $string = mb_strtolower($value);

        $accentedChars = AdminHelper::getAccentedChars();

        $string = str_replace(array_keys($accentedChars), array_values($accentedChars), $string);

        return $string;
    }

    /**
     * @return string
     */
    private function buildSearchQueryWithReplacedAccents()
    {
        $searchString = 'lower(%s.%s)';

        $accents = AdminHelper::getAccentedChars();

        foreach ($accents as $accent => $value) {
            $searchString = sprintf('replace(%s, \'%s\', \'%s\')', $searchString, $accent, $value);
        }

        return $searchString .  ' %s :%s';
    }
}
