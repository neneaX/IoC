<?php
namespace tests\IoC\Container;

use \IoC\Container;

class reset extends \PHPUnit_Framework_TestCase
{
    /**
     * Reset will clear the stored instance.
     * @runInSeparateProcess
     */
    public function testResetWillClearTheStoredInstance()
    {
        $default = new Container;

        $response = Container::getInstance();
        static::assertSame($response, \PHPUnit_Framework_Assert::readAttribute($response, 'instance'));

        Container::reset();
        static::assertNull(\PHPUnit_Framework_Assert::readAttribute($default, 'instance'));
    }
}
