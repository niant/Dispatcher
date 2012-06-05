<?php
namespace Apok\Test\Component\Dispatcher;

use Apok\Component\Dispatcher\Dispatcher;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Fake controller->action for the router to test
     */
    public function route($param1, $param2)
    {
        $this->assertEquals($param1, 'param1');
        $this->assertEquals($param2, 'param2');

        return true;
    }

    /**
     * Fake router matching method
     */
    public function matchRoute($url)
    {
        $array = array(
            'controller' => 'dispatcherTest',
            'action' => 'route',
            'parameter1' => 'param1',
            'parameter2' => 'param2'
        );

        return $array;
    }

    public function setUp()
    {
        Dispatcher::$controllerNamespace = '\Apok\Test\Component\Dispatcher\\';

        // Setup this object as a fake router
        Dispatcher::$router = new DispatcherTest();
        Dispatcher::$routerMatchFunction = 'matchRoute';
    }

    public function testSetFullRequestUrl()
    {
        Dispatcher::setFullRequestUrl('dispatcherTest/route');
        $this->assertEquals(Dispatcher::$requestUrl, 'dispatcherTest/route');
    }

    public function testFullDispatch()
    {
        Dispatcher::dispatch();
    }

    public function testDispatchUrl()
    {
        $routeResult = Dispatcher::dispatchUrl('dispatcherTest/route');
        $this->assertTrue($routeResult);
    }
}