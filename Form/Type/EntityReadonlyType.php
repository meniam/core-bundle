<?php

namespace Meniam\Bundle\CoreBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

class EntityReadonlyType extends AbstractType
{
    public function buildView(FormView $view, FormInterface $form, array $options)
    {}

    public function getParent()
    {
        return EntityType::class;
    }

    public function getBlockPrefix()
    {
        return 'entity_readonly';
    }
}