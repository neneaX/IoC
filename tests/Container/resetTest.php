<?php
namespace tests\IoC\Container;

use \IoC\Container;

class reset extends \PHPUnit_Framework_TestCase
{
    /**
     * Reset will clear the stored instance.
     * 
     * @dataProvider instanceNameDataProvider
     * @runInSeparateProcess
     */
    public function testResetWillClearTheStoredInstance($instanceName)
    {
        $default = new Container;

        $response = Container::getInstance($instanceName);
    
        $instances = \PHPUnit_Framework_Assert::readAttribute($response, 'instances');
        if (!isset($instances[$instanceName])) {
            $selectedInstance = $instances['default'];
        } else {
            $selectedInstance = $instances[$instanceName];
        }
        static::assertSame($response, $selectedInstance);

        Container::reset();
        static::assertNull(\PHPUnit_Framework_Assert::readAttribute($default, 'instances'));
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
