<?php

namespace Wucdbm\Bundle\QuickUIBundle\Form;

use Symfony\Component\Form\AbstractType as SymfonyAbstractType;
use Symfony\Component\Form\Extension\Core\DataTransformer\DateTimeToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HiddenDateType extends SymfonyAbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options) {
        $transformer = new DateTimeToStringTransformer(null, null, $options['format']);
        $builder->addModelTransformer($transformer);
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'format' => 'Y-m-d'
        ]);
    }

    public function getParent() {
        return HiddenType::class;
    }
}