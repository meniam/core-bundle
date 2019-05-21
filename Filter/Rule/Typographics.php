<?php

namespace Meniam\Bundle\CoreBundle\Filter\Rule;

use TypographBundle\EMT\EMTypograph;

class Typographics extends Trim
{
    private static $typo;

    public function filter($value)
    {
        $options = [
            'OptAlign.all' => false,
            'OptAlign.layout' => 'css',
            'Text.paragraphs' => false,
            'Text.auto_links' => false,
            'Text.email' => false,
            'Text.breakline' => false,
            'Text.no_repeat_words' => false
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