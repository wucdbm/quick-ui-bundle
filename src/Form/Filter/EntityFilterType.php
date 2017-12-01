<?php

namespace Wucdbm\Bundle\QuickUIBundle\Form\Filter;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntityFilterType extends AbstractType {

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver) {
        $resolver->setDefaults([
            'attr' => [
                'class' => 'select2'
            ]
        ]);
    }

    public function getParent() {
        return EntityType::class;
    }

}
