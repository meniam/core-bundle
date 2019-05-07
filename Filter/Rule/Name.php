<?php

namespace Meniam\Bundle\CoreBundle\Filter\Rule;

use Meniam\Bundle\CoreBundle\Filter\FilterRule;
use Meniam\Bundle\CoreBundle\Filter\FilterStatic;

class Name extends FilterRule
{
    public function filter($value)
    {
        $value = FilterStatic::filterValue($value, HtmlAndUnicode::class);
        $value = preg_replace('#\s+#', ' ', $value);
        return trim($value, "\r\n\t,:;(^$ ");
    }
}