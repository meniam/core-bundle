<?php

namespace Meniam\Bundle\CoreBundle\Filter\Rule;

use Meniam\Bundle\CoreBundle\Filter\FilterRule;
use Meniam\Bundle\CoreBundle\Filter\FilterStatic;
use Cocur\Slugify\Slugify;

class Slug extends FilterRule
{
    /**
     * @var Slugify
     */
    private static $slugify;

    public function filter($value)
    {
        $value = FilterStatic::filterValue($value, Name::class);
        return $this->getSlugify()->slugify($value);
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

