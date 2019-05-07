<?php

namespace Meniam\Bundle\CoreBundle\Filter\Rule;

use Meniam\Bundle\CoreBundle\Filter\FilterRule;
use Meniam\Bundle\CoreBundle\Filter\FilterStatic;
use Cocur\Slugify\Slugify;

class SeoKeyword extends FilterRule
{
    public function filter($value)
    {
        $value = FilterStatic::filterValue($value, HtmlAndUnicode::class);
        $value = mb_strtolower(preg_replace('#\s+#', ' ', $value), 'UTF-8');
        $words = explode(' ', $value);
        $words = array_map([$this, 'filterWord'], $words);
        return implode(' ', $words);
    }

    private function filterWord($word)
    {
        return trim($word, '"\'()[]{}|-+=');
    }
}

