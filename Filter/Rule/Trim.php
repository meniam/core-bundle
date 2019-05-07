<?php

namespace Meniam\Bundle\CoreBundle\Filter\Rule;

use Meniam\Bundle\CoreBundle\Filter\FilterRule;
use Meniam\Bundle\CoreBundle\Filter\FilterStatic;
use Meniam\Bundle\CoreBundle\Filter\Rule\HtmlAndUnicode;

class Trim extends FilterRule
{
    public function filter($value)
    {
        $value = FilterStatic::filterValue($value, HtmlAndUnicode::class);
        $value = trim(preg_replace('#\s+#', ' ', $value));
        return $value;
    }
}