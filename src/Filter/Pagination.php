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

namespace Wucdbm\Bundle\QuickUIBundle\Filter;

class Pagination {

    const DEFAULT_PAGE = 1;

    const DEFAULT_LIMIT = 20;

    protected $page = self::DEFAULT_PAGE;

    protected $limit = self::DEFAULT_LIMIT;

    protected $totalResults = null;

    protected $minPagesToUseSelect = 10;

    protected $minPagesToUseTextInput = 250;

    protected $route = null;

    protected $params = [];

    protected $range = 3;

    protected $showPrevNext = false;

    protected $showFirstLast = false;

    protected $labels = [
        'first' => '<i class="fa fa-angle-double-left"></i>',
        'last' => '<i class="fa fa-angle-double-right"></i>',
        'prev' => '<i class="fa fa-angle-left"></i>',
        'next' => '<i class="fa fa-angle-right"></i>'
    ];

    protected $enabled = false;

    /**
     * @var AbstractFilter
     */
    private $filter = null;

    /**
     * Returne previous page.
     *
     * @return mixed
     */
    public function getPrevPage() {
        return $this->page - 1 < 1 ? null : $this->page - 1;
    }

    /**
     * Returne next page.
     *
     * @return null|int
     */
    public function getNextPage() {
        return $this->page + 1 > $this->getTotalPages() ? null : $this->page + 1;
    }

    /**
     * Returns offset for use by Doctrine when calculating query offset.
     *
     * @return int
     */
    public function getOffset() {
        $page = ($this->page - 1) >= 0 ? ($this->page - 1) : 0;

        return $page * $this->limit;
    }

    /**
     * Returns total pages based on total items count divided by items per page.
     *
     * @return bool
     */
    public function getTotalPages() {
        if (empty($this->limit)) {
            return 0;
        }

        return ceil($this->totalResults / $this->limit);
    }

    public function useSelect() {
        return $this->getTotalPages() >= $this->getMinPagesToUseSelect();
    }

    public function useTextInput() {
        return $this->getTotalPages() >= $this->getMinPagesToUseTextInput();
    }

    public function has() {
        if ($this->getTotalPages() <= 1) {
            return false;
        }
        $pages = range(1, $this->getTotalPages());
        if (1 === count($pages)) {
            return false;
        }

        return true;
    }

    public function get() {
        $pages = range(1, $this->getTotalPages());

        return $this->make($pages);
    }

    public function range($range = null) {
        if (null === $range) {
            $range = $this->getRange();
        }
        $page = $this->getPage();
        $pages = [$page];
        if ($range > 1) {
            for ($i = 1; $i <= $range; ++$i) {
                $prev = $page - $i;
                $next = $page + $i;
                if ($this->pageExists($prev)) {
                    $pages[] = $prev;
                }
                if ($this->pageExists($next)) {
                    $pages[] = $next;
                }
            }
            sort($pages);

            return $this->make($pages);
        }

        return $this->make($page);
    }

    public function make($pages) {
        $return = [];
        if ($this->isShowFirstLast() && $this->getPrevPage() && 1 != $this->getPrevPage()) {
            $return[$this->labels['first']] = $this->buildPaginationArray(1);
        }
        if ($this->isShowPrevNext() && $this->getPrevPage()) {
            $return[$this->labels['prev']] = $this->buildPaginationArray($this->getPrevPage());
        }
        foreach ($pages as $page) {
            $return[$page] = $this->buildPaginationArray($page);
        }
        if ($this->isShowPrevNext() && $this->getNextPage()) {
            $return[$this->labels['next']] = $this->buildPaginationArray($this->getNextPage());
        }
        if ($this->isShowFirstLast() && $this->getNextPage() && $this->getTotalPages() != $this->getNextPage()) {
            $return[$this->labels['last']] = $this->buildPaginationArray($this->getTotalPages());
        }

        return $return;
    }

    public function pageExists($page) {
        $pages = range(1, $this->getTotalPages());
        if (in_array($page, $pages)) {
            return true;
        }

        return false;
    }

    public function buildPaginationArray($page) {
        $params = $this->getParams();
        $params['page'] = $page;

        return $params;
    }

//    public function getParams() {
//        $get = $this->filter->getPaginationParams();
//        if ($get) {
//            return $get;
//        }
//        return array();
//        return $this->filter->getProtectedValues();
//    }

    public function getVars($skip = null) {
        if (!is_array($skip) && $skip) {
            $skip = [$skip];
        } else {
            $skip = [];
        }
        $params = $this->getParams();
        foreach ($skip as $var) {
            unset($params[$var]);
        }

        return $params;
    }

    public function enable() {
        $this->enabled = true;

        return $this;
    }

    public function disable() {
        $this->enabled = false;

        return $this;
    }

    /**
     * @param $page
     *
     * @return $this
     */
    public function setPage($page) {
        if (!is_numeric($page)) {
            $page = self::DEFAULT_PAGE;
        }

        if (0 == $page) {
            $page = self::DEFAULT_PAGE;
        }

        $this->page = $page;

        return $this;
    }

    /**
     * @param $limit
     *
     * @return $this
     */
    public function setLimit($limit) {
        if (!is_numeric($limit)) {
            $limit = self::DEFAULT_LIMIT;
        }

        if (0 == $limit) {
            // Display ALL Results - No Limit!
            $limit = null;
        }

        $this->limit = $limit;

        return $this;
    }

    /**
     * @param $minPagesToUseSelect
     *
     * @return $this
     */
    public function setMinPagesToUseSelect($minPagesToUseSelect) {
        $this->minPagesToUseSelect = $minPagesToUseSelect;

        return $this;
    }

    /**
     * @param $minPagesToUseTextInput
     *
     * @return $this
     */
    public function setMinPagesToUseTextInput($minPagesToUseTextInput) {
        $this->minPagesToUseTextInput = $minPagesToUseTextInput;

        return $this;
    }

    /**
     * @param $route
     *
     * @return $this
     */
    public function setRoute($route) {
        $this->route = $route;

        return $this;
    }

    /**
     * @param $params
     *
     * @return $this
     */
    public function setParams($params) {
        $this->params = $params;

        return $this;
    }

    /**
     * @param $totalResults
     *
     * @return $this
     */
    public function setTotalResults($totalResults) {
        $this->totalResults = $totalResults;

        return $this;
    }

    /**
     * @param $enabled
     *
     * @return $this
     */
    public function setEnabled($enabled) {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * @param int $range
     */
    public function setRange($range) {
        $this->range = $range;
    }

    /**
     * @param $showPrevNext
     *
     * @return $this
     */
    public function setShowPrevNext($showPrevNext) {
        $this->showPrevNext = $showPrevNext;

        return $this;
    }

    /**
     * @param $showFirstLast
     *
     * @return $this
     */
    public function setShowFirstLast($showFirstLast) {
        $this->showFirstLast = $showFirstLast;

        return $this;
    }

    /**
     * @param AbstractFilter $filter
     *
     * @return $this
     */
    public function setFilter(AbstractFilter $filter) {
        $this->filter = $filter;

        return $this;
    }

    public function __construct(AbstractFilter $filter) {
        $this->setFilter($filter);
    }

    /**
     * @return int
     */
    public function getLimit() {
        return $this->limit;
    }

    /**
     * @return int
     */
    public function getMinPagesToUseSelect() {
        return $this->minPagesToUseSelect;
    }

    /**
     * @return int
     */
    public function getMinPagesToUseTextInput() {
        return $this->minPagesToUseTextInput;
    }

    /**
     * @return int
     */
    public function getPage() {
        return $this->page;
    }

    public function getRoute() {
        return $this->route;
    }

    /**
     * @return array
     */
    public function getParams() {
        return $this->params;
    }

    public function getTotalResults() {
        return $this->totalResults;
    }

    /**
     * @return bool
     */
    public function isEnabled() {
        return $this->enabled;
    }

    /**
     * @return AbstractFilter
     */
    public function getFilter() {
        return $this->filter;
    }

    /**
     * @return int
     */
    public function getRange() {
        return $this->range;
    }

    /**
     * @return bool
     */
    public function isShowPrevNext() {
        return $this->showPrevNext;
    }

    /**
     * @return bool
     */
    public function isShowFirstLast() {
        return $this->showFirstLast;
    }
}
