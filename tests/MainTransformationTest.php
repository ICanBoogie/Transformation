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
use ICanBoogie\Transformation\TestCases\DataSample2;
use ICanBoogie\Transformation\TestCases\DataSample3;
use PHPUnit\Framework\TestCase;

class MainTransformationTest extends TestCase
{
    /**
     * @test
     * @dataProvider provideReturnSame
     *
     * @param mixed $expected
     */
    public function shouldReturnSame($expected)
    {
        $transformation = new MainTransformation(function () {
        });

        $this->assertSame($expected, $transformation($expected));
    }

    public function provideReturnSame()
    {
        return [

            [ null ],
            [ true ],
            [ false ],
            [ mt_rand(10, 20) ],
            [ mt_rand(10, 20) + .5 ],
            [ "A" . uniqid() ],

        ];
    }

    /**
     * @test
     * @dataProvider provideThrowTransformationNotFound
     * @expectedException \ICanBoogie\Transformation\TransformationNotFound
     *
     * @param mixed $data
     */
    public function shouldThrowTransformationNotFound($data)
    {
        $dataTransformation = function ($data, callable $actualTransformation) use (&$transformation) {
            $this->assertSame($transformation, $actualTransformation);

            return get_class($data);
        };

        $transformation = new MainTransformation(function ($data) use ($dataTransformation) {
            if (!$data instanceof DataSample1) {
                throw new TransformationNotFound($data);
            }

            return $dataTransformation;
        });

        $transformation($data);
    }

    public function provideThrowTransformationNotFound()
    {
        return [

            [ new DataSample3 ],
            [ [ new DataSample3 ] ],
            [ [ new DataSample1, new DataSample3 ] ],
            [ new \ArrayIterator([ new DataSample3 ]) ],
            [ new \ArrayIterator([ new DataSample1, new DataSample3 ]) ],

        ];
    }

    /**
     * @test
     * @dataProvider provideTransformData
     *
     * @param mixed $data
     * @param mixed $expected
     */
    public function shouldTransformData($data, $expected)
    {
        $customTransformation = function () {
        };

        $dataTransformation = function ($data, callable $actualTransformation) use ($customTransformation) {
            $this->assertSame($customTransformation, $actualTransformation);

            return get_class($data);
        };

        $transformation = new MainTransformation(function ($data) use ($dataTransformation) {
            if (!$data instanceof DataSample1) {
                throw new TransformationNotFound($data);
            }

            return $dataTransformation;
        });

        $this->assertSame($expected, $transformation($data, $customTransformation));
    }

    /**
     * @test
     * @dataProvider provideTransformData
     *
     * @param mixed $data
     * @param mixed $expected
     */
    public function ShouldCallTransformationWithSpecifiedTransformer($data, $expected)
    {
        $dataTransformation = function ($data, callable $actualTransformation) use (&$transformation) {
            $this->assertSame($transformation, $actualTransformation);

            return get_class($data);
        };

        $transformation = new MainTransformation(function ($data) use ($dataTransformation) {
            if (!$data instanceof DataSample1) {
                throw new TransformationNotFound($data);
            }

            return $dataTransformation;
        });

        $this->assertSame($expected, $transformation($data));
    }

    public function provideTransformData()
    {
        return [

            [ new DataSample1, DataSample1::class ],
            [ [ new DataSample1, new DataSample2 ], [ DataSample1::class, DataSample2::class ] ],
            [ new \ArrayIterator([ new DataSample1, new DataSample2 ]), [ DataSample1::class, DataSample2::class ] ],

        ];
    }
}
