<?php

namespace Meniam\Bundle\CoreBundle\Filter\Rule;

use Meniam\Bundle\CoreBundle\Filter\FilterRule;
use Meniam\Bundle\CoreBundle\Filter\FilterStatic;

class SuggestionSearchId extends FilterRule
{
    public function filter($value)
    {
        $value = FilterStatic::filterValue($value, Name::class);
        if (preg_match('/#(\d+)/usi', $value, $m)) return $m[1];
        if (preg_match('#^\d+$#', $value, $m)) return $m;
        return null;
    }
}