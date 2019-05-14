<?php

namespace Meniam\Bundle\CoreBundle\Service;

interface IdableInterface
{
    public function getId($entity);
    public function getDisplayId($entity);
}
