<?php

namespace Meniam\Bundle\CoreBundle\Form\Type\Button;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ButtonLineSaveType extends SubmitType
{
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'label' => 'button.save_and_return_labeled',
                'button_class' => 'success',
                'attr' => [
                    'icon' => 'save',
                    'align_with_widget' => true,
                    'class' => 'btn-labeled',
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

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'save_button';
    }
}