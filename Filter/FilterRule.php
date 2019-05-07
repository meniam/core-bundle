<?php

namespace Meniam\Bundle\CoreBundle\Filter;

abstract class FilterRule
{
    /**
     * @param mixed $value
     * @return mixed
     */
    abstract function filter($value);
}