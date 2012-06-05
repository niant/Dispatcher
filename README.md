# Dispatcher

Dispatcher component that dispatches routing information to corresponding class and method.

## Usage

### Configuration

Configure namespace for the (controller) class to use:

    Dispatcher::$controllerNamespace = '\Vendor\Controller\ControllerName\\';

Configure router which the dispatcher should use:

    Dispatcher::$router = new Router();

    // Configure routers' function that matches the routes to url and returns
    // array in a format of array('controller' => 'pages', 'action' => 'index', ...)
    Dispatcher::$routerMatchFunction = 'match';

Configure where to find full url request (eg. if $_SERVER['REQUEST_URI'] is not available)

    // Optional
    Dispatcher::setFullRequestUrl($_SERVER['REQUEST_URI']);

### Ready to use

Launch dispatching on full request url

    Dispatcher::dispatch();

or dispatch a specific url

    Dispatcher::dispatchUrl('/controller/action/param1/param2');

or dispatch a specific controller, action and parameters (optional)

    Dispatcher::request('controller', 'action', $parameterArray);
