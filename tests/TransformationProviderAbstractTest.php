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

class TransformationProviderAbstractTest extends TestCase
{
    /**
     * @test
     */
    public function shouldErrorOnTransformationNotFound()
    {
        $provider = $this->makeProvider();
        $data = new DataSample1;

        try {
            $provider($data);
        } catch (TransformationNotFound $e) {
            $this->assertNull($e->getPrevious());
            $this->assertSame($data, $e->getData());
            return;
        }

        $this->fail("Expected TransformationNotFound");
    }

    /**
     * @test
     */
    public function shouldCatchException()
    {
        $exception = new \Exception;
        $provider = $this->makeProvider([

            DataSample1::class => $transformation = uniqid()

        ], function ($actualTransformation) use ($transformation, $exception) {
            $this->assertSame($transformation, $actualTransformation);
            throw $exception;
        });

        $data = new DataSample1;

        try {
            $provider($data);
        } catch (TransformationNotFound $e) {
            $this->assertSame($exception, $e->getPrevious());
            $this->assertSame($data, $e->getData());
            return;
        }

        $this->fail("Expected TransformationNotFound");
    }

    /**
     * @test
     */
    public function shouldResolveTransformation()
    {
        $transformation = function () {
        };

        $provider = $this->makeProvider([

            DataSample3::class => uniqid(),
            DataSample1::class => $transformationId = uniqid()

        ], function ($actualTransformationId) use ($transformationId, $transformation) {
            $this->assertSame($transformationId, $actualTransformationId);
            return $transformation;
        });

        $this->assertSame($transformation, $provider(new DataSample2));
    }

    /**
     * @param array $transformations
     * @param callable|null $assertion
     *
     * @return TransformationProvider
     */
    private function makeProvider(array $transformations = [], callable $assertion = null): TransformationProvider
    {
        return new class ($transformations, $assertion) extends TransformationProviderAbstract
        {
            /**
             * @var callable
             */
            private $resolver;

            /**
             * @inheritdoc
             * @param callable $resolver
             */
            public function __construct(array $transformations, callable $resolver = null)
            {
                $this->resolver = $resolver;

                parent::__construct($transformations);
            }

            /**
             * @inheritdoc
             */
            protected function resolve($transformation): callable
            {
                return ($this->resolver)($transformation);
            }
        };
    }
}
