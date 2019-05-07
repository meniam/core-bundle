<?php

namespace Meniam\Bundle\CoreBundle\Traits;

trait StringTrait
{
    public function lcfirst($str)
    {
        $secondChar = mb_substr($str, 1, 1, 'UTF-8');
        if ($secondChar && !$this->isCharUpperCase($secondChar)) {
            return mb_strtolower(mb_substr($str, 0, 1, 'UTF-8')) . mb_substr($str, 1, mb_strlen($str, 'UTF-8'), 'UTF-8');
        }
        return $str;
    }

    public function ucfirst($str)
    {
        return mb_strtoupper(mb_substr($str, 0, 1, 'UTF-8')) . mb_substr($str, 1, mb_strlen($str, 'UTF-8'), 'UTF-8');
    }

    public function ucwords($str)
    {
        return mb_convert_case($str, MB_CASE_TITLE, "UTF-8");
    }

    public function isCharUpperCase($char)
    {
        $upper = 'ЙЦУКЕНГШЩЗХЪЁЭЖДЛОРПАВЫФЯЧСМИТЬБЮQWERTYUIOPLKJHGFDSAZXCVBNM';
        return (mb_strpos($upper, $char) !== false);
    }
}
