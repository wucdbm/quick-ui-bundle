<?php

namespace Wucdbm\Bundle\QuickUIBundle\Repository;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;

trait IndexByFixTrait {

    /**
     * @param string $alias
     * @param null $indexBy
     * @return QueryBuilder
     */
    public function createQueryBuilder($alias, $indexBy = null) {
        return $this->getEntityManager()->createQueryBuilder()
            ->select($alias)
            ->from($this->getEntityName(), $alias, $indexBy);
    }

    /**
     * @return EntityManager
     */
    protected abstract function getEntityManager();

    /**
     * @return string
     */
    protected abstract function getEntityName();

}