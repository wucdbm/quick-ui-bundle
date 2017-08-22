<?php

namespace Wucdbm\Bundle\QuickUIBundle\Form\Filter;

use Symfony\Component\Form\AbstractType;

abstract class BaseFilterType extends AbstractType {

    public function getParent() {
        return BasicFilterType::class;
    }

    public function getBlockPrefix() {
        return '';
    }

}