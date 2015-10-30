<?php
namespace tests\IoC\Resolver;

use IoC\Builder;
use IoC\Resolver;

class execute extends \PHPUnit_Framework_TestCase
{
    public function tearDown()
    {
        \Mockery::close();
    }

    /**
     * When called will return the stored builder's value.
     */
    public function testWhenCalledWillStoreTheValues()
    {
        $object = new \stdClass();
        $object->id = 1085;

        $builder = new Builder($object);
        $resolver = new Resolver($builder);

        $response = $resolver->execute();

         static::assertSame($object, $response);
    }

    /**
     * When not shared and prepared response instance already initialized then will rebuild the response instance
     */
    public function testWhenNotSharedAndPreparedResponseInstanceAlreadyInitializedThenWillRebuildTheResponseInstance()
    {
        $object = new \stdClass();
        $object->id = 1085;

        $builder = new Builder($object);
        $resolver = new Resolver($builder);

        $preparedInstance = new \stdClass();
        $preparedInstance->id = 104;

        $refObject   = new \ReflectionObject($resolver);
        $refProperty = $refObject->getProperty('instance');
        $refProperty->setAccessible(true);
        $refProperty->setValue($resolver, $preparedInstance);

        static::assertSame($object, $resolver->execute());
    }

    /**
     * When shared and prepared response instance already initialized then will return the stored response instance
     */
    public function testWhenSharedAndPreparedResponseInstanceAlreadyInitializedThenWillReturnTheStoredResponseInstance()
    {
        $object = new \stdClass();
        $object->id = 1085;

        $builder = new Builder($object);
        $resolver = new Resolver($builder, true);

        $preparedInstance = new \stdClass();
        $preparedInstance->id = 104;

        $refObject   = new \ReflectionObject($resolver);
        $refProperty = $refObject->getProperty('instance');
        $refProperty->setAccessible(true);
        $refProperty->setValue($resolver, $preparedInstance);

        static::assertSame($preparedInstance, $resolver->execute());
    }

    /**
     * When parameters are passed then apply and return response.
     */
    public function testWhenParametersArePassedThenApplyAndReturnResponse()
    {
        $builder = new Builder(function ($numeric, $currencySymbol) {
            return number_format($numeric, 2) . ' ' . $currencySymbol;
        });

        $resolver = new Resolver($builder);

        static::assertEquals('1.23 €', $resolver->execute([1.2345, '€']));
    }

    /**
     * When instance is shared then rebuild only if stored response instance is not an object.
     */
    public function testWhenInstanceIsSharedThenRebuildOnlyIfStoredResponseInstanceIsNotAnObject()
    {
        $builder = new Builder(function ($numeric, $currencySymbol) {
            return number_format($numeric, 2) . ' ' . $currencySymbol;
        });

        $resolver = new Resolver($builder, true);

        static::assertEquals('1.23 €', $resolver->execute([1.2345, '€']));
        static::assertEquals('5.43 €', $resolver->execute([5.4321, '€']));
    }

    /**
     * When instance is not shared then rebuild when calling again.
     */
    public function testWhenInstanceIsNotSharedThenDontRebuildWhenCallingAgain()
    {
        $builder = new Builder(function ($numeric, $currencySymbol) {
            return number_format($numeric, 2) . ' ' . $currencySymbol;
        });

        $resolver = new Resolver($builder, false);

        static::assertEquals('1.23 €', $resolver->execute([1.2345, '€']));
        static::assertEquals('5.43 €', $resolver->execute([5.4321, '€']));
    }

    /**
     * When instance is shared and stored response is an object then dont rebuild.
     */
    public function testWhenInstanceIsSharedAndStoredResponseIsAnObjectThenDontRebuild()
    {
        $object = new \stdClass();
        $builder = new Builder($object);

        /** @var \Mockery\MockInterface $resolver */
        $resolver = \Mockery::mock('\IoC\Resolver', [$builder, true])->makePartial();
        $resolver->shouldAllowMockingProtectedMethods();
        $resolver->shouldReceive('build')->once()->passthru();

        static::assertSame($object, $resolver->execute());
        static::assertSame($object, $resolver->execute());
    }

    /**
     * When instance is not shared then rebuild on each call.
     */
    public function testWhenInstanceIsNotSharedThenRebuildOnEachCall()
    {
        $object = new \stdClass();
        $builder = new Builder($object);

        /** @var \Mockery\MockInterface $resolver */
        $resolver = \Mockery::mock('\IoC\Resolver', [$builder, false])->makePartial();
        $resolver->shouldAllowMockingProtectedMethods();
        $resolver->shouldReceive('build')->twice()->passthru();

        static::assertSame($object, $resolver->execute());
        static::assertSame($object, $resolver->execute());
    }
}
