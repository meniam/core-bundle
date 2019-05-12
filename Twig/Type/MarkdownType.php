<?php

namespace Meniam\Bundle\CoreBundle\Twig\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MarkdownType extends AbstractType
{
    public function getParent()
    {
        return TextareaType::class;
    }

    public function getBlockPrefix()
    {
        return 'markdown';
    }
}