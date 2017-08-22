<?php

namespace Wucdbm\Bundle\QuickUIBundle\Form\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlaceholderType extends AbstractType {

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'mapped' => false,
            'required' => false,
            'attr' => [
                'class' => 'placeholder',
                'placeholder' => 'Period: From - To'
            ]
        ]);
    }

    public function getParent() {
        return TextType::class;
    }

}
