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

use PHPUnit\Framework\TestCase;

class ContextualizedTransformationTest extends TestCase
{
    /**
     * @test
     */
    public function shouldReturnAContext()
    {
        $transformation = new ContextualizedTransformation;

        $this->assertInstanceOf(Context::class, $transformation->getContext());
    }

    /**
     * @test
     */
    public function shouldReturnSameContext()
    {
        $transformation = new ContextualizedTransformation($context = new Context);

        $this->assertSame($context, $transformation->getContext());
    }

    /**
     * @test
     */
    public function shouldUseTransformationAndUpdateContext()
    {
        $data = uniqid();
        $result = uniqid();
        $transformation = function ($actualData, $actualTransformation) use ($data, $result, &$ctxTransformation) {
            $this->assertSame($data, $actualData);
            $this->assertSame($ctxTransformation, $actualTransformation);
            return $result;
        };

        $ctxTransformation = $this->makeTransformation(
            function ($context) use ($data) {
                $context->push($data)
                    ->shouldBeCalled();
                $context->pop()
                    ->shouldBeCalled();
            },

            $transformation
        );

        $this->assertSame($result, $ctxTransformation($data));
    }

    /**
     * @test
     */
    public function shouldCloneSelfWhenTransformationIsProvidedWithInvoke()
    {
        $data = uniqid();
        $result = uniqid();

        $ctxTransformation = new ContextualizedTransformation($context = new Context());

        $transformation = function (
            $actualData,
            ContextualizedTransformation $actualTransformation
        ) use (
            $data,
            $result,
            $ctxTransformation,
            $context
        ) {
            $this->assertSame($data, $actualData);
            $this->assertNotSame($ctxTransformation, $actualTransformation);
            $this->assertNotSame($context, $actualTransformation->getContext());

            return $result;
        };

        $this->assertSame($result, $ctxTransformation($data, $transformation));
    }

    /**
     * @param callable|null $initContext
     * @param callable|null $transformation
     *
     * @return ContextualizedTransformation
     */
    private function makeTransformation(
        callable $initContext = null,
        callable $transformation = null
    ): ContextualizedTransformation {
        return new ContextualizedTransformation(
            $this->mockContext($initContext),
            $transformation
        );
    }

    /**
     * @param callable|null $init
     *
     * @return Context
     */
    private function mockContext(callable $init = null): Context
    {
        $context = $this->prophesize(Context::class);

        if ($init) {
            $init($context);
        }

        return $context->reveal();
    }
}
