<?php

namespace Meniam\Bundle\CoreBundle\Twig\Extension\Form;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TabExtension extends AbstractTypeExtension
{
    public static function getExtendedTypes()
    {
        return [FormType::class];
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(['tab']);

        $resolver->setDefaults([
            'tab' => [
                'namespace' => null,
                'name' => null,
                'label' => null
            ]
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $namespace = $options['tab']['namespace'];
        $tabName = isset($options['tab']['name']) ? $options['tab']['name'] : $namespace;
        $tabLabel = isset($options['tab']['label']) ? $options['tab']['label'] : $namespace;

        if (null === $namespace) {
            return;
        }

        $root = $this->getRootView($view);
        if (!isset($root->vars['tabs'][$namespace][$tabName])) {
            $root->vars['tabs'][$namespace][$tabName] =
                [
                    'name' => $tabName,
                    'label' => $tabLabel,
                ];
        }

        if (!isset($root->vars['tabs'][$namespace][$tabName]['elements'])) {
            $root->vars['tabs'][$namespace][$tabName]['elements'] = [$form->getName()];
        } else {
            $root->vars['tabs'][$namespace][$tabName]['elements'][] = $form->getName();
        }
    }

    public function getRootView(FormView $view)
    {
        $root = $view->parent;

        while (null === $root) {
            $root = $root->parent;
        }

        return $root;
    }
}