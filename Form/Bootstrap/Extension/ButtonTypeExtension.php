<?php

namespace Meniam\Bundle\CoreBundle\Form\Bootstrap\Extension;

use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\Extension\Core\Type\ButtonType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

/**
 * FormControlStaticType
 *
 * @package    BraincraftedBootstrapBundle
 * @subpackage Form
 * @author     André Püschel <pue@der-pue.de>
 * @copyright  2014 André Püschel
 * @license    http://opensource.org/licenses/MIT The MIT License
 * @link       http://bootstrap.braincrafted.com Bootstrap for Symfony2
 */
class ButtonTypeExtension extends AbstractTypeExtension
{
    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        $view->vars['button_class'] = $form->getConfig()->getOption('button_class');
        $view->vars['as_link'] = $form->getConfig()->getOption('as_link');
    }

    /**
     * Add the button_class option
     * Add the as_link option
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefined(array('button_class', 'as_link'));
    }

    /**
     * Return the class of the type being extended.
     */
    public static function getExtendedTypes(): iterable
    {
        // return FormType::class to modify (nearly) every field in the system
        return [ButtonType::class];
    }
}