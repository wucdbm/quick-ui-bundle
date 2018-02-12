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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BasicFilterType extends AbstractType {

    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {
        if ($options['enable_limit']) {
            $builder->add('limit', ChoiceType::class, [
                'choices' => [
                    'All' => 0,
                    '10 Results' => 10,
                    '20 Results' => 20,
                    '50 Results' => 50,
                    '100 Results' => 100,
                    '250 Results' => 250,
                    '500 Results' => 500,
                    '1000 Results' => 1000
                ],
                'label' => false,
                'attr' => [
                    'class' => 'select2'
                ]
            ]);

            $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($builder) {
                // This is to make sure that if the limit was not passed in the request,
                // the default value of the model would be used and shown when the form is rendered,
                // instead of showing 'All' in the select, but limiting to the default instead
                $data = $event->getData();
                if (!isset($data['limit'])) {
                    $form = $event->getForm();
                    if ($form->has('limit')) {
                        $data['limit'] = $form->get('limit')->getData();
                        $event->setData($data);
                    }
                }
            });
        }
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'attr' => [
                'class' => 'autosubmit filter-form'
            ],
            'method' => 'GET',
            'csrf_protection' => false,
            'allow_extra_fields' => true,
            'enable_limit' => true
        ]);
    }

    public function getBlockPrefix() {
        return '';
    }
}
