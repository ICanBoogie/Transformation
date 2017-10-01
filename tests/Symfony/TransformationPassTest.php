<?php

/*
 * This file is part of the ICanBoogie package.
 *
 * (c) Olivier Laviale <olivier.laviale@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ICanBoogie\Transformation\Symfony;

use ICanBoogie\Transformation\PSR\TransformationProvider;
use ICanBoogie\Transformation\TestCases\DataSample1;
use ICanBoogie\Transformation\TestCases\DataSample1Transformation;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class TransformationPassTest extends TestCase
{
    const RESOURCES = __DIR__ . DIRECTORY_SEPARATOR . 'resources';

    /**
     * @test
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     * @expectedExceptionMessageRegExp  /The `type` property is required for service .+DataSample1Transformation/
     */
    public function shouldErrorOnMissingProperty()
    {
        $this->makeContainer('missing-type-property.yml');
    }

    /**
     * @test
     * @expectedException \Symfony\Component\DependencyInjection\Exception\LogicException
     * @expectedExceptionMessageRegExp  /The type `.+DataSample1` already has a transformation/
     */
    public function shouldErrorOnDuplicateType()
    {
        $this->makeContainer('duplicate-type.yml');
    }

    /**
     * @test
     * @dataProvider provideCompile
     *
     * @param string $file
     * @param TransformationPass|null $pass
     * @param string $serviceId
     */
    public function shouldCompile(
        string $file,
        TransformationPass $pass = null,
        string $serviceId = TransformationPass::DEFAULT_SERVICE_ID
    ) {
        /* @var TransformationProvider $provider */
        $container = $this->makeContainer($file, $pass);
        $provider = $container->get($serviceId);

        $this->assertInstanceOf(TransformationProvider::class, $provider);
        $this->assertInstanceOf(DataSample1Transformation::class, $provider(new DataSample1));
    }

    public function provideCompile()
    {
        $serviceId = uniqid();

        return [

            [ 'ok.yml' ],

            [ 'ok.yml', new TransformationPass(
                $serviceId
            ), $serviceId ],

            [ 'custom-tag.yml', new TransformationPass(
                TransformationPass::DEFAULT_SERVICE_ID,
                'custom-tag'
            ) ],

            [ 'custom-type.yml', new TransformationPass(
                TransformationPass::DEFAULT_SERVICE_ID,
                TransformationPass::DEFAULT_TAG,
                'custom-type'
            ) ],

        ];
    }

    /**
     * @param string $file
     *
     * @param TransformationPass|null $pass
     *
     * @return ContainerBuilder
     */
    private function makeContainer(string $file, TransformationPass $pass = null): ContainerBuilder
    {
        $container = new ContainerBuilder();
        $loader = new YamlFileLoader($container, new FileLocator(self::RESOURCES));
        $loader->load($file);
        $container->addCompilerPass($pass ?: new TransformationPass);
        $container->compile();

        return $container;
    }
}
