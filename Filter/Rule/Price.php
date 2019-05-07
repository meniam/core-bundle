<?php

namespace Meniam\Bundle\CoreBundle\Filter\Rule;

use Meniam\Bundle\CoreBundle\Filter\FilterRule;

class Price extends FilterRule
{
    public function filter($value)
    {
        $value = preg_replace('#[^\d\.\,\-]+#usi', '', strip_tags($value));
        $value = preg_replace('#[\.\,\-]+$#usi', '', $value);
        $value = preg_replace('#[\,\.]+#usi', '.', $value);
        return (float)$value;
    }
}