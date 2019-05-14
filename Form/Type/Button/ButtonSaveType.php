<?php

namespace Meniam\Bundle\CoreBundle\Form\Type\Button;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ButtonSaveType extends SubmitType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'label' => 'button.save_labeled',
                'button_class' => 'success',
                'translation_domain' => 'messages',
                'attr' => [
                    'icon' => 'fal-save',
                    'align_with_widget' => true,
                    'class' => 'btn-lg btn-labeled',
                    'label_col' => 0,
                    'widget_col' => 12
                ]
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getParent()
    {
        return SubmitType::class;
    }
}