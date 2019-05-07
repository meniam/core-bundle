<?php

namespace Meniam\Bundle\CoreBundle\Filter\Rule;

use Meniam\Bundle\CoreBundle\Filter\FilterRule;
use Meniam\Bundle\CoreBundle\Filter\FilterStatic;

class StopwordsUniq extends FilterRule
{
    public function filter($value)
    {
        $value = explode("\n", $value);
        foreach ($value as &$v) {
            $v = FilterStatic::filterValue($v, SphinxQuery::class);
        }
        $value = array_filter($value);
        $value = array_unique($value);
        return trim(implode("\n", $value));
    }
}