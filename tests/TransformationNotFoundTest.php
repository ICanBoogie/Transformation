<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\Transformation;

use ICanBoogie\Transformation\TestCases\DataSample1;
use PHPUnit\Framework\TestCase;

class TransformationNotFoundTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnData()
    {
        $exception = new TransformationNotFound($data = uniqid());

        $this->assertSame($data, $exception->getData());
    }

    /**
     * @test
     * @dataProvider provideFormatMessage()
     *
     * @param mixed $data
     * @param string $expected
     */
    public function shouldFormatMessage($data, string $expected)
    {
        $this->assertSame($expected, (new TransformationNotFound($data))->getMessage());
    }

    public function provideFormatMessage()
    {
        return [

            [ 'a' . uniqid(), "Transformation not found for type string." ],
            [ true, "Transformation not found for type boolean." ],
            [ new DataSample1, sprintf("Transformation not found for instance of %s.", DataSample1::class) ],

        ];
    }
}
