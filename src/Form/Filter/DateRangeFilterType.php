<?php

/*
 * This file is part of the WucdbmQuickUIBundle package.
 *
 * Copyright (c) Martin Kirilov <martin@forci.com>
 *
 * Author Martin Kirilov <martin@forci.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Wucdbm\Bundle\QuickUIBundle\Form\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Wucdbm\Bundle\QuickUIBundle\Form\HiddenDateType;

class DateRangeFilterType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        $builder
            ->add($options['min_field_name'], HiddenDateType::class, [
                'attr' => [
                    'class' => 'hidden min'
                ]
            ])
            ->add($options['max_field_name'], HiddenDateType::class, [
                'attr' => [
                    'class' => 'hidden max'
                ]
            ])
            ->add('placeholder', PlaceholderType::class, [
                'attr' => [
                    'rel' => 'tooltip',
                    'title' => $options['placeholder'],
                    'placeholder' => $options['placeholder']
                ]
            ])
        ;
    }

    public function buildView(FormView $view, FormInterface $form, array $options) {
        $view->vars['min_field_name'] = $options['min_field_name'];
        $view->vars['max_field_name'] = $options['max_field_name'];
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'inherit_data' => true,
            'min_field_name' => 'date_min',
            'max_field_name' => 'date_max',
            'placeholder' => 'Period: From - To',
        ]);
    }
}
