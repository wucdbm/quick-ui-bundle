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
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChoiceFilterType extends AbstractType {

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'required' => false,
            'placeholder' => ''
        ]);
    }

    public function buildView(FormView $view, FormInterface $form, array $options) {
        $view->vars = array_replace_recursive($view->vars, [
            'attr' => [
                'rel' => 'tooltip',
                'title' => $options['placeholder'],
                'placeholder' => $options['placeholder'],
                'class' => 'select2'
            ]
        ]);
    }

    public function getParent() {
        return ChoiceType::class;
    }
}
