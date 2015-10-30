<?php
namespace tests\IoC\Resolver;

use IoC\Builder;
use IoC\Resolver;

class construct extends \PHPUnit_Framework_TestCase
{
    /**
     * When called will store the values.
     * @dataProvider dataProviderBoolValues
     */
    public function testWhenCalledWillStoreTheValues($bool)
    {
        $builder = new Builder('JPY');
        $resolver = new Resolver($builder, $bool);

        static::assertSame($bool, \PHPUnit_Framework_Assert::readAttribute($resolver, 'shared'));
        static::assertSame($builder, \PHPUnit_Framework_Assert::readAttribute($resolver, 'builder'));
    }

    /**
     * By default the stored object will not be marked as shared.
     */
    public function testByDefaultTheObjectStoredWillNotBeMarkedAsShared()
    {
        $builder = new Builder(new \stdClass());
        $resolver = new Resolver($builder);

        static::assertFalse(\PHPUnit_Framework_Assert::readAttribute($resolver, 'shared'));
        static::assertSame($builder, \PHPUnit_Framework_Assert::readAttribute($resolver, 'builder'));
    }

    /**
     * When sending non-boolean as shared value then cast to bool.
     */
    public function testWhenSendingNonBooleanAsSharedValueThenCastToBool()
    {
        $builder = new Builder('JPY');

        static::assertTrue(\PHPUnit_Framework_Assert::readAttribute(new Resolver($builder, 1), 'shared'));
        static::assertFalse(\PHPUnit_Framework_Assert::readAttribute(new Resolver($builder, 0), 'shared'));
        static::assertTrue(\PHPUnit_Framework_Assert::readAttribute(new Resolver($builder, 'meh'), 'shared'));
        static::assertTrue(\PHPUnit_Framework_Assert::readAttribute(new Resolver($builder, new \stdClass()), 'shared'));
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
