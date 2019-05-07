<?php

namespace Meniam\Bundle\CoreBundle\Filter;

class Filter
{
    /**
     * {@inheritDoc}
     */
    public function filterValue($value, $filter)
    {
        if ($filter instanceof FilterRule) {
            return $filter->filter($value);
        }

        return $this->walkRuleChain($value, $filter);
    }

    /**
     * @param mixed $value
     * @param array|FilterRule[] $filters
     * @return mixed
     */
    protected function walkRuleChain($value, $filters)
    {
        /** @var array|FilterRule[] $filters */
        foreach($filters as $filter) {
            $value = $filter->filter($value);
        }

        return $value;
    }

}