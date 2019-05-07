<?php

namespace Meniam\Bundle\CoreBundle\Filter\Rule;

use Meniam\Bundle\CoreBundle\Filter\FilterRule;

class Text extends FilterRule
{
    public function filter($value)
    {
        $value = trim(preg_replace('#\s+#', ' ', $value));
        return trim($value);
    }
}