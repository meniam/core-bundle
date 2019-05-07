<?php

namespace Meniam\Bundle\CoreBundle\Filter\Rule;

use Meniam\Bundle\CoreBundle\Filter\FilterRule;
use Meniam\Bundle\CoreBundle\Filter\FilterStatic;

class SuggestionSearch extends FilterRule
{
    public function filter($value)
    {
        $value = FilterStatic::filterValue($value, Name::class);
        $value = str_replace(['\"', '\'' ], ' ' , $value);
        $value = '%' . mb_strtolower(trim(preg_replace('#\s+#', '%', $value)), 'UTF-8') . '%';
        return $value;
    }
}