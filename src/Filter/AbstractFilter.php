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

use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class AbstractFilter.
 */
class AbstractFilter {

    /**
     * Page Request var name.
     *
     * @var string
     */
    private $pageVar = 'page';

    /**
     * Limit Request var name.
     *
     * @var string
     */
    private $limitVar = 'limit';

    /**
     * String NS for loading vars from Symfony Request object.
     *
     * @var string
     */
    private $namespace = '';

    /**
     * Request type - GET or POST.
     *
     * @var string
     */
    private $type = 'GET';

    /**
     * @var Pagination
     */
    private $pagination = null;

    private $paginationParams = [];

    private $reflection = null;

    /** @var float */
    private $queryExecTime = 0;

    private $_options = [
        self::OPTION_HYDRATION => self::OPTION_HYDRATION_OBJECT
    ];

    const OPTION_HYDRATION = 'hydration';
    /* Hydration mode constants */
    /**
     * Hydrates an object graph. This is the default behavior.
     */
    const OPTION_HYDRATION_OBJECT = 1;
    /**
     * Hydrates an array graph.
     */
    const OPTION_HYDRATION_ARRAY = 2;
    /**
     * Hydrates a flat, rectangular result set with scalar values.
     */
    const OPTION_HYDRATION_SCALAR = 3;
    /**
     * Hydrates a single scalar value.
     */
    const OPTION_HYDRATION_SINGLE_SCALAR = 4;
    /**
     * Very simple object hydrator (optimized for performance).
     */
    const OPTION_HYDRATION_SIMPLEOBJECT = 5;

    public function getHydrationMode() {
        return $this->getOption(self::OPTION_HYDRATION);
    }

    public function setHydrationObject() {
        return $this->setOption(self::OPTION_HYDRATION, self::OPTION_HYDRATION_OBJECT);
    }

    public function setHydrationArray() {
        return $this->setOption(self::OPTION_HYDRATION, self::OPTION_HYDRATION_ARRAY);
    }

    public function isHydrationArray() {
        return $this->isOption(self::OPTION_HYDRATION, self::OPTION_HYDRATION_ARRAY);
    }

    public function getOption($name) {
        return $this->_options[$name];
    }

    public function setOption($name, $value) {
        $this->_options[$name] = $value;

        return $this;
    }

    public function isOption($name, $value) {
        return $this->_options[$name] == $value;
    }

    /**
     * @return null|\ReflectionClass
     */
    public function getReflection() {
        if (null === $this->reflection) {
            $this->reflection = new \ReflectionClass($this);
        }

        return $this->reflection;
    }

    /**
     * @param Request $request
     * @param null    $type
     * @param string  $namespace
     */
    protected function _load(Request $request, $type = null, $namespace = '') {
        $bag = $this->getBagByType($request, $type);
        $vars = $this->getVars($bag, $namespace);
        $page = array_key_exists($this->getPageVar(), $vars) ? $vars[$this->getPageVar()] : 1;

        if (array_key_exists($this->getLimitVar(), $vars)) {
            $limit = $vars[$this->getLimitVar()];
            $this->setLimit($limit);
        }

        $this->setPage($page);
        $pagination = $this->getPagination();
        $pagination->setPage($page);
        $pagination->setParams(array_merge_recursive($bag->all(), $request->get('_route_params')));
        $pagination->setRoute($request->get('_route'));
    }

    public function getLimit() {
        return $this->getPagination()->getLimit();
    }

    public function setLimit($limit) {
        $this->getPagination()->setLimit($limit);
    }

    public function getPage() {
        return $this->getPagination()->getPage();
    }

    public function setPage($page) {
        $this->getPagination()->setPage($page);
    }

    /**
     * @param Request $request
     * @param string  $namespace
     * @param null    $type
     *
     * @return $this
     */
    public function loadFromRequest(Request $request, $namespace = '', $type = null) {
        if (null === $type) {
            $type = $this->getType();
        }

        $this->_load($request, $type, $namespace);
        $bag = $this->getBagByType($request, $this->getType());

        $fields = $this->getProtectedVars();
        $vars = $this->getVars($bag, $namespace);
        foreach ($fields as $field) {
            $val = array_key_exists($field, $vars) ? $vars[$field] : null;
            if ($val) {
                $this->$field = $val;
            }
        }

        return $this;
    }

    protected function getVars(ParameterBag $bag, $namespace) {
        if ($namespace) {
            return $bag->get($namespace, []);
        }

        return $bag->all();
    }

    /**
     * @return $this
     */
    public function extractPaginationParams($route) {
        $vars = [];
        $fields = $this->getProtectedVars();
        foreach ($fields as $field) {
            if ($this->$field) {
                $vars[$field] = $this->$field;
            }
        }
        $pagination = $this->getPagination();
        $pagination->setPage($this->getPage());
        $pagination->setLimit($this->getLimit());
        $pagination->setParams($vars);
        $pagination->setRoute($route);

        return $this;
    }

    /**
     * @param Request       $request
     * @param FormInterface $form
     *
     * @return $this
     */
    public function load(Request $request, FormInterface $form) {
        $this->_load($request, $form->getConfig()->getMethod(), $form->getName());
        $form->handleRequest($request);

        return $this;
    }

    /**
     * @param Request $request
     * @param $type
     *
     * @return ParameterBag
     */
    public function getBagByType(Request $request, $type = null) {
        if ('POST' == $type) {
            return $request->request;
        }
        if ('GET' == $type) {
            return $request->query;
        }

        return $request->query;
    }

    /**
     * @return array
     */
    public function getProtectedVars() {
        $reflection = $this->getReflection();
        $vars = $reflection->getProperties(\ReflectionProperty::IS_PROTECTED);
        $ret = [];
        foreach ($vars as $var) {
            $ret[] = $var->name;
        }

        return $ret;
    }

    /**
     * @return $this
     */
    public function enablePagination() {
        $this->getPagination()->enable();

        return $this;
    }

    public function getMd5() {
        return md5(serialize($this));
    }

    /**
     * @param $name
     * @param $value
     *
     * @throws \Exception
     */
    public function __set($name, $value) {
        $reflection = $this->getReflection();
        if (!$reflection->hasProperty($name)) {
            throw $this->createPropertyMissingException($name);
        }
        $this->$name = $value;
    }

    /**
     * @param $name
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function __get($name) {
        $reflection = $this->getReflection();
        if (!$reflection->hasProperty($name)) {
            throw $this->createPropertyMissingException($name);
        }

        return $this->$name;
    }

    protected function createPropertyMissingException($name) {
        return new \Exception('Filter '.get_class($this).' does not have property ['.$name.']. Maybe you forgot to implement it first?');
    }

    public function getQueryExecTime(): float {
        return $this->queryExecTime;
    }

    public function setQueryExecTime(float $queryExecTime) {
        $this->queryExecTime = $queryExecTime;
    }

    public function __construct() {
        $pagination = new Pagination($this);
        $this->setPagination($pagination);
    }

    /**
     * @return array
     */
    public function getOptions() {
        return $this->_options;
    }

    /**
     * @param array $options
     */
    public function setOptions($options) {
        $this->_options = $options;
    }

    /**
     * @return Pagination
     */
    public function getPagination() {
        return $this->pagination;
    }

    /**
     * @param Pagination $pagination
     */
    public function setPagination($pagination) {
        $this->pagination = $pagination;
    }

    /**
     * @return array
     */
    public function getPaginationParams() {
        return $this->paginationParams;
    }

    /**
     * @param array $paginationParams
     */
    public function setPaginationParams($paginationParams) {
        $this->paginationParams = $paginationParams;
    }

    /**
     * @return string
     */
    public function getPageVar() {
        return $this->pageVar;
    }

    /**
     * @param string $pageVar
     */
    public function setPageVar($pageVar) {
        $this->pageVar = $pageVar;
    }

    /**
     * @return string
     */
    public function getLimitVar() {
        return $this->limitVar;
    }

    /**
     * @param string $limitVar
     */
    public function setLimitVar($limitVar) {
        $this->limitVar = $limitVar;
    }

    /**
     * @return string
     */
    public function getNamespace() {
        return $this->namespace;
    }

    /**
     * @param string $namespace
     */
    public function setNamespace($namespace) {
        $this->namespace = $namespace;
    }

    /**
     * @return string
     */
    public function getType() {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType($type) {
        $this->type = $type;
    }
}
