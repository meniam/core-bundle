<?php

namespace App\Traits;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Adapter\FixedAdapter;
use Pagerfanta\Pagerfanta;

trait PagerTrait
{
    public function createArrayPager($page, $perPage, $items, $itemsCount)
    {
        $page = $page > 0 ? (int)$page : 1;
        $perPage = $perPage > 0 ? (int)$perPage : 100;

        $itemsCount = min($itemsCount, $perPage * 100);

        $pager = (new Pagerfanta(new FixedAdapter($itemsCount, $items)))
            ->setAllowOutOfRangePages(true)
            ->setNormalizeOutOfRangePages(true)
            ->setMaxPerPage($perPage)
            ->setCurrentPage($page);

        return $pager;
    }

    public function createQueryPager($query, $page, $perPage = 30)
    {
        $page = $page > 0 ? (int)$page : 1;
        $perPage = $perPage > 0 ? (int)$perPage : 100;

        $pager = (new Pagerfanta(new DoctrineORMAdapter($query)))
            ->setAllowOutOfRangePages(true)
            ->setNormalizeOutOfRangePages(true)
            ->setMaxPerPage($perPage)
            ->setCurrentPage($page);

        return $pager;
    }
}
