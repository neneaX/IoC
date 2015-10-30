<?php
namespace tests\IoC\Container;

use \IoC\Container;

class singleton extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        $container = Container::getInstance();

        $refObject   = new \ReflectionObject($container);
        $refProperty = $refObject->getProperty('registry');
        $refProperty->setAccessible(true);
        $refProperty->setValue($container, []);
    }

    /**
     * When called will store a resolver instance with the given alias to the registry listing.
     */
    public function testWhenCalledWillStoreAResolverInstanceWithTheGivenAliasToTheRegistryListing()
    {
        $this->tearDown();

        $binding = new \InvalidArgumentException('What?');

        $container = Container::getInstance();
        $container->singleton('invalidArgumentException', $binding);

        $registry = \PHPUnit_Framework_Assert::readAttribute($container, 'registry');

        static::assertTrue(is_array($registry));
        static::assertEquals(1, count($registry));
        static::assertArrayHasKey('invalidArgumentException', $registry);
        static::assertInstanceOf('\IoC\Resolver', $registry['invalidArgumentException']);
        static::assertInstanceOf('\InvalidArgumentException', $registry['invalidArgumentException']->execute());
    }

    /**
     * Will always set the resolver as shared.
     * @depends testWhenCalledWillStoreAResolverInstanceWithTheGivenAliasToTheRegistryListing
     */
    public function testWillAlwaysSetTheResolverAsShared()
    {
        $container = Container::getInstance();

        $container->singleton('cashValidator', function ($numeric) {
            return is_numeric($numeric) && $numeric >= 0;
        });

        $registry = \PHPUnit_Framework_Assert::readAttribute($container, 'registry');

        $markedAsShared = \PHPUnit_Framework_Assert::readAttribute($registry['cashValidator'], 'shared');
        static::assertTrue($markedAsShared);
    }
}
