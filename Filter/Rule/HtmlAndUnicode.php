<?php

namespace Meniam\Bundle\CoreBundle\Filter\Rule;

use Meniam\Bundle\CoreBundle\Filter\FilterRule;

class HtmlAndUnicode extends FilterRule
{
    public function filter($value)
    {
        $value = html_entity_decode($value, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $value = html_entity_decode($value, ENT_QUOTES | ENT_XML1, 'UTF-8'); // Double convert for &amp;quot;
        $value = strip_tags($value);
        return preg_replace('/\\\u[A-F\d]{2,5}/si', '', $value);
    }
}
