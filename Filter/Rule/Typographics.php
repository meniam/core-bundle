<?php

namespace Meniam\Bundle\CoreBundle\Filter\Rule;

use Meniam\Bundle\CoreBundle\Filter\FilterRule;
use TypographBundle\EMT\EMTypograph;

class Typographics extends FilterRule
{
    private static $typo;

    public function filter($value)
    {
        $options = [
            'OptAlign.all' => false,
            'OptAlign.layout' => 'css'
        ];

        return self::getTypo()->fast_apply($value, $options);
    }

    /**
     * @return EMTypograph
     */
    private static function getTypo()
    {
        if (!self::$typo) {
            self::$typo = new EMTypograph();
        }

        return self::$typo;
    }
}