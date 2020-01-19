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

namespace Wucdbm\Bundle\QuickUIBundle\Form;

use Symfony\Component\Form\AbstractType as SymfonyAbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TextDateType extends SymfonyAbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $transformer = new DateTimeToStringTransformer(
            $options['input_timezone'], $options['output_timezone'], $options['format']
        );
        $builder->addModelTransformer($transformer);
    }

    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'format' => 'Y-m-d',
            'input_timezone' => null,
            'output_timezone' => null
        ]);
    }

    public function getParent() {
        return TextType::class;
    }
}
