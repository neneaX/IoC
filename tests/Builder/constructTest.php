<?php
namespace tests\IoC\Builder;

use IoC\Builder;

class construct extends \PHPUnit_Framework_TestCase
{
    /**
     * When called will store the input on the binding attribute.
     * @dataProvider dataProviderBindingSamples
     */
    public function testWhenCalledWillStoreTheInputOnTheBindingAttribute($input, $validation)
    {
        $object = new Builder($input);
        $binding = \PHPUnit_Framework_Assert::readAttribute($object, 'binding');

        static::assertTrue($validation($binding));
    }

    public function dataProviderBindingSamples()
    {
        return [
            // case: closure without parameters
            [
                'input' => function ()
                {
                    return [
                        'ID'  => 'user1',
                        'key' => '*****************'
                    ];
                },
                'validation' => function ($input)
                {
                    if (!($input instanceof \Closure)) {
                        throw new \PHPUnit_Framework_Exception('The input is not a Closure');
                    }

                    $response = call_user_func($input);
                    if (!is_array($response) || $response['ID'] !== 'user1' || count(($response)) !== 2) {
                        throw new \PHPUnit_Framework_Exception('The input was malformed');
                    }

                    return true;
                }
            ],
            // case: closure with parameters
            [
                'input' => function ($datetimeString, $prefix = '0+')
                {
                    return $prefix . strtotime($datetimeString);
                },
                'validation' => function ($input)
                {
                    if (!($input instanceof \Closure)) {
                        throw new \PHPUnit_Framework_Exception('The input is not a Closure');
                    }

                    $response = call_user_func_array($input, ['2015-08-26 23:59:59', 'cata_']);
                    $expected = 'cata_' . strtotime('2015-08-26 23:59:59');

                    if ($response !== $expected) {
                        throw new \PHPUnit_Framework_Exception(
                            'The input was malformed, ' .
                            'expected [' . $expected . '] but got [' . json_encode($response) . ']'
                        );
                    }

                    return true;
                }
            ],
            // case: object
            [
                'input' => new \DOMDocument(),
                'validation' => function ($input)
                {
                    if (!($input instanceof \DOMDocument)) {
                        throw new \PHPUnit_Framework_Exception('The input is not the sent object.');
                    }

                    return true;
                }
            ],
            // case: string
            [
                'input' => '676f6564656d6f7267656e',
                'validation' => function ($input)
                {
                     return ($input === '676f6564656d6f7267656e');
                }
            ],
            // case: numeric
            [
                'input' => 4.19,
                'validation' => function ($input)
                {
                    return (4.19 === $input);
                }
            ],
            // case: unicorns
            [
                'input' => new \InvalidArgumentException('Invalid interval.'),
                'validation' => function ($input)
                {
                    return ($input instanceof \InvalidArgumentException && $input->getMessage() === 'Invalid interval.');
                }
            ]
        ];
    }
}
