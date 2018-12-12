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

namespace Wucdbm\Bundle\QuickUIBundle\Twig;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class ControllerActionName extends \Twig_Extension {

    /** @var RequestStack */
    protected $stack;

    /** @var string */
    protected $currentRoute = '';

    public function __construct(RequestStack $stack) {
        $this->stack = $stack;
    }

    public function getFilters() {
        return [
            new \Twig_SimpleFilter('isActionAndController', [$this, 'isActionAndController']),
            new \Twig_SimpleFilter('isController', [$this, 'isController']),
            new \Twig_SimpleFilter('isAction', [$this, 'isAction']),
            new \Twig_SimpleFilter('isRoute', [$this, 'isRoute']),
            new \Twig_SimpleFilter('routeStartsWith', [$this, 'routeStartsWith'])
        ];
    }

    public function getFunctions() {
        return [
            new \Twig_SimpleFunction('controllerName', [$this, 'controllerName']),
            new \Twig_SimpleFunction('actionName', [$this, 'actionName']),
            new \Twig_SimpleFunction('isActionAndController', [$this, 'isActionAndController']),
            new \Twig_SimpleFunction('isController', [$this, 'isController']),
            new \Twig_SimpleFunction('isAction', [$this, 'isAction']),
            new \Twig_SimpleFunction('isRoute', [$this, 'isRoute'])
        ];
    }

    /**
     * Get current controller name.
     */
    public function controllerName() {
        $request = $this->stack->getCurrentRequest();
        if ($request instanceof Request) {
            $string = $request->get('_controller');
            $parts = explode('::', $string);
            $controller = $parts[0];
            $pattern = "#Controller\\\([a-zA-Z\\\]*)Controller#";
            $matches = [];
            preg_match($pattern, $controller, $matches);
            if (isset($matches[1])) {
                return strtolower(str_replace('\\', '_', $matches[1]));
            }

            return '';
        }

        return '';
    }

    /**
     * Get current action name.
     */
    public function actionName() {
        $request = $this->stack->getCurrentRequest();
        if ($request instanceof Request) {
            $pattern = '#::([a-zA-Z]*)Action#';
            $matches = [];
            preg_match($pattern, $request->get('_controller'), $matches);
            if (isset($matches[1])) {
                return strtolower($matches[1]);
            }

            return '';
        }

        return '';
    }

    /**
     * Get current route name.
     */
    public function routeName() {
        if (null === $this->currentRoute) {
            $request = $this->stack->getCurrentRequest();

            if ($request instanceof Request) {
                $this->currentRoute = $request->get('_route');
            }
        }

        return $this->currentRoute;
    }

    public function isRoute($route, $print = '') {
        if (is_array($route)) {
            foreach ($route as $rt) {
                if ($this->_isRoute($rt)) {
                    return $print;
                }
            }
        } elseif (is_string($route)) {
            if ($this->_isRoute($route)) {
                return $print;
            }
        }

        return '';
    }

    protected function _isRoute($route) {
        return $this->routeName() == $route;
    }

    public function routeStartsWith($route, $print = '') {
        if (is_array($route)) {
            foreach ($route as $rt) {
                if ($this->_routeStartsWith($rt)) {
                    return $print;
                }
            }
        } elseif (is_string($route)) {
            if ($this->_routeStartsWith($route)) {
                return $print;
            }
        }

        return '';
    }

    protected function _routeStartsWith($route): bool {
        return 0 === strpos($this->routeName(), $route);
    }

    public function isController($controller, $print = '') {
        if (is_array($controller)) {
            foreach ($controller as $ctrl) {
                if ($this->_isController($ctrl)) {
                    return $print;
                }
            }
        } elseif (is_string($controller)) {
            if ($this->_isController($controller)) {
                return $print;
            }
        }

        return '';
    }

    protected function _isController($controller) {
        return $this->controllerName() == $controller;
    }

    public function isAction($action, $print = '') {
        if (is_array($action)) {
            foreach ($action as $act) {
                if ($this->_isAction($act)) {
                    return $print;
                }
            }
        } elseif (is_string($action)) {
            if ($this->_isAction($action)) {
                return $print;
            }
        }

        return '';
    }

    protected function _isAction($action) {
        return $this->actionName() == $action;
    }

    public function isActionAndController($action, $controller, $print = '') {
        if ($this->_isAction($action) && $this->_isController($controller)) {
            return $print;
        }

        return '';
    }
}
