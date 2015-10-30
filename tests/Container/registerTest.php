<?php
namespace tests\IoC\Container;

use \IoC\Container;

class register extends \PHPUnit_Framework_TestCase
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
     * By default the internal registry will not have any entries stored.
     */
    public function testByDefaultTheInternalRegistryWillNotHaveAnyEntriesStored()
    {
        $container = Container::getInstance();
        $registry = \PHPUnit_Framework_Assert::readAttribute($container, 'registry');

        static::assertTrue(is_array($registry));
        static::assertEquals(0, count($registry));
    }

    /**
     * When called will store a resolver instance with the given alias to the registry listing.
     */
    public function testWhenCalledWillStoreAResolverInstanceWithTheGivenAliasToTheRegistryListing()
    {
        $binding = new \InvalidArgumentException('What?');

        $container = Container::getInstance();
        $container->register('invalidArgumentException', $binding);

        $registry = \PHPUnit_Framework_Assert::readAttribute($container, 'registry');

        static::assertTrue(is_array($registry));
        static::assertEquals(1, count($registry));
        static::assertArrayHasKey('invalidArgumentException', $registry);
        static::assertInstanceOf('\IoC\Resolver', $registry['invalidArgumentException']);
        static::assertInstanceOf('\InvalidArgumentException', $registry['invalidArgumentException']->execute());
    }

    /**
     * By default will set the resolver as not shared.
     * @depends testWhenCalledWillStoreAResolverInstanceWithTheGivenAliasToTheRegistryListing
     */
    public function testByDefaultWillSetTheResolverAsNotShared()
    {
        $container = Container::getInstance();
        $container->register('cashValidator', function ($numeric) {
            return is_numeric($numeric) && $numeric >= 0;
        });

        $registry = \PHPUnit_Framework_Assert::readAttribute($container, 'registry');

        $markedAsShared = \PHPUnit_Framework_Assert::readAttribute($registry['cashValidator'], 'shared');
        static::assertFalse($markedAsShared);
    }

    /**
     * When specified will set the resolver as shared flag.
     * @dataProvider dataProviderBoolValues
     * @depends testWhenCalledWillStoreAResolverInstanceWithTheGivenAliasToTheRegistryListing
     */
    public function testWhenSpecifiedWillSetTheResolverAsSharedFlag($flag)
    {
        $container = Container::getInstance();
        $container->register('settings', ['a', 'b'], $flag);

        $registry = \PHPUnit_Framework_Assert::readAttribute($container, 'registry');
        $markedAsShared = \PHPUnit_Framework_Assert::readAttribute($registry['settings'], 'shared');

        static::assertSame($flag, $markedAsShared);
    }

    /**
     * When called multiple times will append to the registry list.
     */
    public function testWhenCalledMultipleTimesWillAppendToTheRegistryList()
    {
        $container = Container::getInstance();

        $registry = \PHPUnit_Framework_Assert::readAttribute($container, 'registry');
        static::assertEquals(0, count($registry));

        // first
        $container->register('version', '1');

        $registry = \PHPUnit_Framework_Assert::readAttribute($container, 'registry');
        static::assertEquals(1, count($registry));

        // second
        $container->register('usage', function () {
            return 1/0;
        });

        $registry = \PHPUnit_Framework_Assert::readAttribute($container, 'registry');
        static::assertEquals(2, count($registry));
    }

    /**
     * When called with an existing alias then overwrite the binding.
     */
    public function testWhenCalledWithAnExistingAliasThenOverwriteTheBinding()
    {
        $container = Container::getInstance();

        $object1 = new \stdClass();
        $object1->id = 1;

        $object2 = new \stdClass();
        $object2->id = 2;

        // first
        $container->register('version', $object1);

        // second
        $container->register('usage', function () {
            return PHP_OS;
        });

        // check the soon-to-be previous value
        $registry = \PHPUnit_Framework_Assert::readAttribute($container, 'registry');
        static::assertSame($object1, $registry['version']->execute());

        // overwrite first bind
        $container->register('version', $object2);

        $registry = \PHPUnit_Framework_Assert::readAttribute($container, 'registry');
        static::assertEquals(2, count($registry));
        static::assertSame($object2, $registry['version']->execute());
    }

    public function dataProviderBoolValues()
    {
        return [
            [
                true
            ],
            [
                false
            ]
        ];
    }
}
