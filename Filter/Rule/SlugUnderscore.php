<?php

namespace Meniam\Bundle\CoreBundle\Filter\Rule;

use Cocur\Slugify\Slugify;

class SlugUnderscore extends Name
{
    /**
     * @var Slugify
     */
    private static $slugify;

    public function filter($value)
    {
        $value = parent::filter($value);
        return trim($this->getSlugify()->slugify($value, ['separator' => '_']), '_');
    }

    /**
     * @return Slugify
     */
    private function getSlugify()
    {
        if (!self::$slugify) {
            self::$slugify = new Slugify();
        }

        return self::$slugify;
    }
}

