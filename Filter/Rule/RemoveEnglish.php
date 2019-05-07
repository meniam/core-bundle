<?php

namespace Meniam\Bundle\CoreBundle\Filter\Rule;

use Meniam\Bundle\CoreBundle\Filter\FilterRule;

class RemoveEnglish extends FilterRule
{
    public function filter($value)
    {
        $value = htmlspecialchars_decode($value, ENT_QUOTES);
        $value = (new AccentsReplace())->filter($value);

        // Убираем все не буквы и пытаемся определить,
        // есть ли буковки отличные от английских
        $value = preg_replace('#[^\p{L}\p{N}]++#u', ' ', $value);
        return preg_replace('#[a-z0-9 ]#si', '', $value);
    }
}