<?php
namespace tests\IoC\Container;

use \IoC\Container;

class getInstance extends \PHPUnit_Framework_TestCase
{
    /**
     * By default the Container doesnt have any value stored as the instance.
     * @runInSeparateProcess
     */
    public function testByDefaultTheInstanceKeyDoesntHaveAnyValueStored()
    {
        $default = new Container;
        $instanceAttributeValue = \PHPUnit_Framework_Assert::readAttribute($default, 'instance');

        static::assertNull($instanceAttributeValue);
    }

    /**
     * Stores the instance in the container.
     */
    public function testStoresTheInstanceInTheContainer()
    {
        $default = new Container;

        $response = $default::getInstance();
        $instanceAttributeValue = \PHPUnit_Framework_Assert::readAttribute($response, 'instance');

        static::assertSame($response, $instanceAttributeValue);
    }

    /**
     * Returns an instance of IoC\Container
     */
    public function testGetInstanceReturnsAnInstanceOfIoCContainer()
    {
        $response = Container::getInstance();

        static::assertInstanceOf('\IoC\Container', $response);
    }

    /**
     * Returns the same instance of Container every time it is called.
     */
    public function testReturnsTheSameInstanceOfContainerEveryTimeItIsCalled()
    {
        $response1 = Container::getInstance();
        $response2 = Container::getInstance();

        static::assertSame($response1, $response2);
    }
}