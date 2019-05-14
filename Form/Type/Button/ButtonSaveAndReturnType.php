<?php

namespace Meniam\Bundle\CoreBundle\Form\Type\Button;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ButtonSaveAndReturnType extends SubmitType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'label' => 'button.save_and_return_labeled',
                'button_class' => 'outline-secondary btn-save-and-return',
                'attr' => [
                    'icon' => 'fal-retweet',
                    'align_with_widget' => true,
                    'class' => 'btn-lg',
                    'label_col' => 0, 'widget_col' => 12
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