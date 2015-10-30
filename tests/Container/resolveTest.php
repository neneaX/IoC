<?php
namespace tests\IoC\Container;

use IoC\Builder;
use \IoC\Container;
use IoC\Resolver;

class resolve extends \PHPUnit_Framework_TestCase
{
    /**
     * When the given handle name does not have a registered closure then throw exception
     * @expectedExceptionMessage No class found registered with that name: notSet
     * @expectedException \Exception
     */
    public function testWhenTheGivenHandleNameDoesNotHaveARegisteredClosureThenThrowException()
    {
        $container = Container::getInstance();
        $container->resolve('notSet');
    }

    /**
     * When the given handle name has a registered resolver then return closure response.
     */
    public function testWhenTheGivenHandleNameHasARegisteredResolverThenReturnClosureResponse()
    {
        $container = Container::getInstance();

        $container->register('configuration', function ($series, $number) {
            $response = new \stdClass();
            $response->identifier = $series . $number;
            return $response;
        });

        $response = null;

        try {
            $response = $container->resolve('configuration', ['KT', '123456']);

        } catch (\Exception $e) {
            static::fail($e->getMessage());
        }

        static::assertInstanceOf('\stdClass', $response);
        static::assertEquals('KT123456', $response->identifier);
    }

    /**
     * When the given name has a registered object (via the singleton method) then return said object.
     */
    public function testWhenTheGivenNameHasARegisteredObjectViaTheSingletonMethodThenReturnSaidObject()
    {
        $container = Container::getInstance();

        $UUID = uniqid('identifier', true);

        $resolver = function ($identifier, $quantity) {
            return new ResolverObjectUnderTest($identifier, $quantity);
        };

        $container->singleton('logger', $resolver);

        $response = $container->resolve('logger', [$UUID, 500]);

        static::assertInstanceOf('\tests\IoC\Container\ResolverObjectUnderTest', $response);
        static::assertEquals($UUID, $response->getId());
        static::assertEquals(500, $response->getQty());
    }

    /**
     * If the instance stored is neither stdClass nor Closure then throw exception.
     * @expectedExceptionMessage No class found registered with that name: hiThere
     * @expectedException \Exception
     */
    public function testIfTheInstanceStoredIsNeitherStdClassNorClosureThenThrowException()
    {
        $container = Container::getInstance();

        $refObject   = new \ReflectionObject($container);
        $refProperty = $refObject->getProperty('registry');
        $refProperty->setAccessible(true);
        $refProperty->setValue($container, array(
            'notHere' => new Resolver(new Builder(''))
        ));

        $container->resolve('hiThere');
    }
}

class ResolverObjectUnderTest
{
    protected $identifier;
    protected $qty;

    public function __construct($identifier, $qty)
    {
        $this->identifier = $identifier;
        $this->qty = $qty;
    }

    public function getId()
    {
        return $this->identifier;
    }

    public function getQty()
    {
        return $this->qty;
    }
}
