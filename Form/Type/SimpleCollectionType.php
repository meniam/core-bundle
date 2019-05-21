<?php

namespace Meniam\Bundle\CoreBundle\Form\Type;

use Meniam\Bundle\CoreBundle\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SimpleCollectionType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars = array_replace(
            $view->vars,
            array(
                'allow_add' => $options['allow_add'],
                'allow_delete' => $options['allow_delete'],
                'add_button_text' => $options['add_button_text'],
                'add_button_row_class' => $options['add_button_row_class'],
                'delete_button_text' => $options['delete_button_text'],
                'delete_button_row_class' => $options['delete_button_row_class'],
                'sub_widget_col' => $options['sub_widget_col'],
                'button_col' => $options['button_col'],
                'prototype_name' => $options['prototype_name'],
            )
        );

        if (false === $view->vars['allow_delete']) {
            $view->vars['sub_widget_col'] += $view->vars['button_col'];
        }

        if ($form->getConfig()->hasAttribute('prototype')) {
            $view->vars['prototype'] = $form->getConfig()->getAttribute('prototype')->createView($view);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $optionsNormalizer = function (Options $options, $value) {
            // @codeCoverageIgnoreStart
            $value['block_name'] = 'entry';

            return $value;
            // @codeCoverageIgnoreEnd
        };

        $resolver->setDefaults(
            array(
                'allow_add' => false,
                'allow_delete' => false,
                'prototype' => true,
                'prototype_name' => '__name__',
                'entry_type' => TextType::class,
                'add_button_row_class' => '',
                'delete_button_row_class' => '',
                'add_button_text' => 'Добавить',
                'delete_button_text' => 'Удалить',
                'sub_widget_col' => 9,
                'button_col' => 3,
                'options' => array(),
            )
        );

        $resolver->setNormalizer('options', $optionsNormalizer);
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return CollectionType::class;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return $this->getBlockPrefix();
    }

    /**
     * {@inheritDoc}
     */
    public function getBlockPrefix()
    {
        return 'simple_collection';
    }
}
