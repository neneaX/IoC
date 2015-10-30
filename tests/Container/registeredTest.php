<?php
namespace tests\IoC\Container;

use \IoC\Container;

class registered extends \PHPUnit_Framework_TestCase
{
    /**
     * When a handle name is registered then return true.
     */
    public function testWhenAHandleNameIsRegisteredThenReturnTrue()
    {
        $container = Container::getInstance();

        $container->register('setComponentRegistered', function () {
            return new \stdClass();
        });

        static::assertTrue($container->registered('setComponentRegistered'));
    }

    /**
     * When a handle name is not registered then return false.
     */
    public function testWhenAHandleNameIsNotRegisteredThenReturnFalse()
    {
        $container = Container::getInstance();

        static::assertFalse($container->registered('missingComponentRegistered'));
    }
}
