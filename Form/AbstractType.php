<?php

namespace Meniam\Bundle\CoreBundle\Form;

use Symfony\Component\Form\AbstractType as AbstractBaseType;
use Symfony\Component\OptionsResolver\OptionsResolver;

abstract class AbstractType extends AbstractBaseType
{
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'csrf_protection'   => false,
            ]);
    }
}
