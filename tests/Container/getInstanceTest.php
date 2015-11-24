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
        $instanceAttributeValue = \PHPUnit_Framework_Assert::readAttribute($default, 'instances');

        static::assertNull($instanceAttributeValue);
    }

    /**
     * Stores the instance in the container.
     * 
     * @dataProvider instanceNameDataProvider
     */
    public function testStoresTheInstanceInTheContainer($instanceName)
    {
        $default = new Container;

        $response = $default::getInstance($instanceName);
        
        $instances = \PHPUnit_Framework_Assert::readAttribute($response, 'instances');
        if (!isset($instances[$instanceName])) {
            $selectedInstance = $instances['default'];
        } else {
            $selectedInstance = $instances[$instanceName];
        }

        static::assertSame($response, $selectedInstance);
    }

    /**
     * Returns an instance of IoC\Container
     * 
     * @dataProvider instanceNameDataProvider
     */
    public function testGetInstanceReturnsAnInstanceOfIoCContainer($instanceName)
    {
        $response = Container::getInstance($instanceName);

        static::assertInstanceOf('\IoC\Container', $response);
    }

    /**
     * Returns the same instance of Container every time it is called.
     * 
     * @dataProvider instanceNameDataProvider
     */
    public function testReturnsTheSameInstanceOfContainerEveryTimeItIsCalled($instanceName)
    {
        $response1 = Container::getInstance($instanceName);
        $response2 = Container::getInstance($instanceName);

        static::assertSame($response1, $response2);
    }
    
    /**
     * 
     * @return multitype:multitype:string
     */
    public function instanceNameDataProvider()
    {
        return [
            [null],
            [''],
            ['test'],
            ['default'],
            ['123'],
            ['wobbly'],
            ['snake_case'],
            ['white space'],
            ['camelCase'],
            ['StuddlyCaps'],
            ['CAPS']
        ];
    }
    
}