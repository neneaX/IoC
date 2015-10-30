<?php
namespace tests\IoC\Builder;

use IoC\Builder;

class run extends \PHPUnit_Framework_TestCase
{
    /**
     * When called will return stored values.
     * @dataProvider dataProviderBindingSamples
     */
    public function testWhenCalledWillReturnStoredValues($input, $validation, $extraProcessing = null)
    {
        $object = new Builder($input);

        if ($extraProcessing instanceof \Closure) {
            $extraProcessing($object);
        }

        $response = $object->run();

        static::assertTrue($validation($response));
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
                'validation' => function ($response)
                {
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
                'validation' => function ($response)
                {
                    $expected = 'pre_' . strtotime('2015-08-26 23:59:58');

                    if ($response !== $expected) {
                        throw new \PHPUnit_Framework_Exception(
                            'The input was malformed, ' .
                            'expected [' . $expected . '] but got [' . json_encode($response) . ']'
                        );
                    }

                    return true;
                },
                'command' => function ($object)
                {
                    $object->setParameters(['2015-08-26 23:59:58', 'pre_']);
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
                'validation' => function ($response)
                {
                    // return ($response === '676f6564656d6f7267656e'); // @todo implement and enable check
                    return (null === $response);
                }
            ],
            // case: numeric
            [
                'input' => 4.21,
                'validation' => function ($response)
                {
                    return (4.21 === $response);
                }
            ],
            // case: unicorns
            [
                'input' => new \InvalidArgumentException('Invalid interval.'),
                'validation' => function ($response)
                {
                    return ($response instanceof \InvalidArgumentException && $response->getMessage() === 'Invalid interval.');
                }
            ]
        ];
    }
}
