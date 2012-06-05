<?php
namespace Apok\Component\Dispatcher;

/**
 * URL dispatching
 */
abstract class Dispatcher
{
    public static $router;

    public static $routerMatchFunction;

    public static $webDirectory = '/';

    public static $controllerNamespace = 'App\Controller\\';

    public static $requestUrl;

    /**
     * Disabled controllers array
     */
    public static $disabledControllers = array();

    /**
     * Disabled methods array
     */
    public static $disabledMethods = array('__construct');

    /**
     *
     */
    public static function makeController($controller)
    {
        $controllerObject = null;
        $controller = ucfirst($controller);

        if (self::isValidController($controller)) {
            $controller = self::$controllerNamespace.$controller;
            $controllerObject = new $controller();
        }

        return $controllerObject;
    }

    /**
     * Check if valid controller
     */
    public static function isValidController($controller)
    {
        $valid = false;

        if (class_exists(self::$controllerNamespace.$controller) &&
           !in_array($controller, self::$disabledControllers)) {
            $valid = true;
        }

        return $valid;
    }

    /**
     * Check if valid action on controller
     */
    public static function isValidAction($controller, $action)
    {
        $valid  = false;
        $method = array($controller, $action);

        if (is_callable($method) and !in_array($action, self::$disabledMethods)) {
            $valid = true;
        }

        return $valid;
    }

    /**
     * Encode url parameters (raw)
     *
     * @param   array $params Parameters (GET) Default NULL (OPTIONAL)
     * @return  array $params Parameters raw encoded if any
     */
    private static function rawDecodeParams(array $params=null)
    {
        if (is_array($params) and count($params)>0) {
            foreach ($params as $key => $value) {
                $value = rawurlencode($value);
                $params[$key] = rawurldecode($value);
            }
        }

        return $params;
    }

    /**
     * Set request URL
     */
    public static function setFullRequestUrl($url=null)
    {
        if ($url && (!self::$requestUrl || self::$requestUrl)) {
            self::$requestUrl = $url;
        } elseif (!$url && !self::$requestUrl && isset($_SERVER['REQUEST_URI'])) {
            self::$requestUrl = $_SERVER['REQUEST_URI'];
        } elseif (!$url && !self::$requestUrl && !isset($_SERVER['REQUEST_URI'])) {
            throw new \Exception('No request URL defined');
        }

        return self::$requestUrl;
    }

    /**
     * Dispatch request url (by default: $_SERVER['REQUEST_URI'])
     */
    public static function dispatch()
    {
        $url = self::setFullRequestUrl();

        if (isset(self::$webDirectory) && self::$webDirectory !== '/') {
            $url = substr($url, strlen(self::$webDirectory));
        }

        return self::dispatchUrl($url);
    }

    /**
     * Dispatch url
     *
     * @param   string $url Url to dispatch
     */
    public static function dispatchUrl($url)
    {
        // remove query parameters from url
        $url = explode('?', $url);
        $url = $url[0];

        $function = self::$routerMatchFunction;
        $params = self::$router->$function($url);

        return self::dispatchRoute($params);
    }

    /**
     * Dispatch route
     */
    public static function dispatchRoute($params)
    {
        if (isset($params['controller']) && isset($params['action'])) {
            $controller = $params['controller'];
            unset($params['controller']);

            $action = $params['action'];
            unset($params['action']);
        } elseif (!isset($params['controller'])){
            throw new \Exception('No controller defined');
        } elseif (!isset($params['action'])){
            throw new \Exception('No action defined');
        }

        return self::request($controller, $action, $params);
    }

    /**
     * Make request for controller with action and parameters
     *
     * @param   string $controller Controller to call
     * @param   string $action Controllers method to call
     * @param   array $params Parameters for Controllers method
     */
    public static function request($controller, $action, $params=array())
    {
        $controller = self::makeController($controller);

        if ($controller && self::isValidAction($controller, $action)) {
            $method = array($controller, $action);
            $params = self::rawDecodeParams($params);
        } elseif (!$controller) {
            throw new \Exception('Invalid controller');
        } else {
            throw new \Exception('Invalid action');
        }

        return call_user_func_array($method, $params);
    }
}