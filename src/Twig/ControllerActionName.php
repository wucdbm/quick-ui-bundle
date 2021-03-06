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
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ControllerActionName extends AbstractExtension {

    /** @var RequestStack */
    protected $stack;

    /** @var string */
    protected $currentRoute = '';

    public function __construct(RequestStack $stack) {
        $this->stack = $stack;
    }

    public function getFilters() {
        return [
            new TwigFilter('isActionAndController', [$this, 'isActionAndController']),
            new TwigFilter('isController', [$this, 'isController']),
            new TwigFilter('isAction', [$this, 'isAction']),
            new TwigFilter('isRoute', [$this, 'isRoute']),
            new TwigFilter('routeStartsWith', [$this, 'routeStartsWith'])
        ];
    }

    public function getFunctions() {
        return [
            new TwigFunction('controllerName', [$this, 'controllerName']),
            new TwigFunction('actionName', [$this, 'actionName']),
            new TwigFunction('isActionAndController', [$this, 'isActionAndController']),
            new TwigFunction('isController', [$this, 'isController']),
            new TwigFunction('isAction', [$this, 'isAction']),
            new TwigFunction('isRoute', [$this, 'isRoute'])
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
        if (!$this->currentRoute) {
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

    public function routeStartsWith($routes, $print = ''): string {
        if (!is_array($routes)) {
            $routes = [$routes];
        }

        foreach ($routes as $route) {
            if (is_array($route)) {
                $not = $route['not'];
                $route = $route['route'];

                if (is_array($not)) {
                    foreach ($not as $notRoute) {
                        if ($this->_routeStartsWith($notRoute)) {
                            continue 2;
                        }
                    }
                } elseif (is_string($not) && $this->_routeStartsWith($not)) {
                    continue;
                }

                if ($this->_routeStartsWith($route)) {
                    return $print;
                }
            } elseif (is_string($route)) {
                if ($this->_routeStartsWith($route)) {
                    return $print;
                }
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
