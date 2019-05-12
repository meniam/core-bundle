<?php

namespace Meniam\Bundle\CoreBundle\Twig\Extension\Form;

use Symfony\Component\Form\Extension\Core\Type\ButtonType;

class TabButtonExtension extends TabExtension
{
    public static function getExtendedTypes()
    {
        return [ButtonType::class];
    }
}