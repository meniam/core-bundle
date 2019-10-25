<?php

namespace Meniam\Bundle\CoreBundle\Filter\Rule;

use Meniam\Bundle\CoreBundle\Filter\FilterRule;
use Meniam\Bundle\CoreBundle\Filter\FilterStatic;

class Name extends FilterRule
{
    public function filter($value)
    {
        $value = FilterStatic::filterValue($value, HtmlAndUnicode::class);
        $value = preg_replace('#\b(\w+\b|\w+)\s*\.\.\.\s*$#usi', '', $value);
        $value = preg_replace('#(\S)\'(s)#si', '\\1\\2', $value);
        $value = preg_replace('#[^\w\d\)\]]+$#usi',' ', $value);
        $value = preg_replace('#\s+#', ' ', $value);
        $value = FilterStatic::filterValue(trim($value, "\r\n\t,:;(^$ "), Ucfirst::class);
        $value = preg_replace('#\s+(это|как|так|и|в|над|к|до|не|на|но|за|то|с|ли|а|во|от|со|для|о|же|ну|вы|бы|что|кто|он|при|около|она)$#usi', '', $value);

        return $value;
    }
}