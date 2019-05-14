<?php

namespace Meniam\Bundle\CoreBundle\Form\Type;

use Meniam\Bundle\CoreBundle\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class MarkdownType extends AbstractType
{
    public function getParent()
    {
        return TextareaType::class;
    }

    public function getName()
    {
        return $this->getBlockPrefix();
    }

    public function getBlockPrefix()
    {
        return 'markdown';
    }
}