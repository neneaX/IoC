<?php
namespace tests\IoC\Builder;

use IoC\Builder;

class setParameters extends \PHPUnit_Framework_TestCase
{
    /**
     * When called with input will store the value on the parameters attribute.
     * @dataProvider dataProviderArrays
     */
    public function testWhenCalledWithInputWillStoreTheValueOnTheParametersAttribute($input)
    {
        $object = new Builder('JPY');
        $object->setParameters($input);

        static::assertEquals($input, \PHPUnit_Framework_Assert::readAttribute($object, 'parameters'));
    }

    public function dataProviderArrays()
    {
        return [
            [
                []
            ],
            [
                ['z', 'y', 'x']
            ]
        ];
    }
}
