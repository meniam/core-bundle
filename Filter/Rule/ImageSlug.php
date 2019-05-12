<?php

namespace Meniam\Bundle\CoreBundle\Filter\Rule;

class ImageSlug extends Slug
{
    public function filter($value)
    {
        $value = parent::filter($value);
        if (strpos($value, '-')) {
            $slug = implode('-', array_slice(explode('-', $value), 0, 10));
        } else {
            $slug = $value;
        }

        $slug = $slug ?: 'item';

        return $slug;
    }
}