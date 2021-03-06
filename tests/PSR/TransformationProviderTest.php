<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\Transformation\PSR;

use ICanBoogie\Transformation\TestCases\DataSample1;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class TransformationProviderTest extends TestCase
{
    /**
     * @test
     */
    public function shouldResolveTransformation()
    {
        $transformation = function () {
        };

        $provider = new TransformationProvider([

            DataSample1::class => $transformationId = uniqid()

        ], $this->mockContainer(function ($container) use ($transformationId, $transformation) {
            $container->get($transformationId)
                ->shouldBeCalledTimes(1)->willReturn($transformation);
        }));

        $this->assertSame($transformation, $provider(new DataSample1));
        # The transformation should be cached
        $this->assertSame($transformation, $provider(new DataSample1));
    }

    /**
     * @param callable|null $init
     *
     * @return ContainerInterface
     */
    private function mockContainer(callable $init = null): ContainerInterface
    {
        $container = $this->prophesize(ContainerInterface::class);

        if ($init) {
            $init($container);
        }

        return $container->reveal();
    }
}
