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

abstract class BaseFilterType extends AbstractType {

    public function getParent() {
        return BasicFilterType::class;
    }

    public function getBlockPrefix() {
        return '';
    }
}
