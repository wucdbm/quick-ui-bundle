<?php

namespace Wucdbm\Bundle\QuickUIBundle\Form\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextFilterType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {

    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'required'    => false,
            'placeholder' => ''
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options) {
        $view->vars = array_replace_recursive($view->vars, [
            'attr' => [
                'rel'         => 'tooltip',
                'title'       => $options['placeholder'],
                'placeholder' => $options['placeholder']
            ]
        ]);
    }

    public function getParent() {
        return TextType::class;
    }

}
