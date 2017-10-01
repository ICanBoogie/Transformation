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
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\LogicException;
use Symfony\Component\DependencyInjection\TypedReference;

/**
 * Registers the transformation provider.
 */
class TransformationPass implements CompilerPassInterface
{
    const DEFAULT_SERVICE_ID = 'transformation.provider';
    const DEFAULT_TAG = 'transformation';
    const DEFAULT_TYPE_PROPERTY = 'type';

    /**
     * @var string
     */
    private $serviceId;

    /**
     * @var string
     */
    private $tag;

    /**
     * @var string
     */
    private $typeProperty;

    /**
     * @param string $serviceId
     * @param string $tag
     * @param string $typeProperty
     */
    public function __construct(
        $serviceId = self::DEFAULT_SERVICE_ID,
        $tag = self::DEFAULT_TAG,
        $typeProperty = self::DEFAULT_TYPE_PROPERTY
    ) {
        $this->serviceId = $serviceId;
        $this->tag = $tag;
        $this->typeProperty = $typeProperty;
    }

    /**
     * @inheritdoc
     */
    public function process(ContainerBuilder $container)
    {
        list($mapping, $refMap) = $this->buildMappingAndRefMap($container);

        $container
            ->register($this->serviceId, TransformationProvider::class)
            ->setArguments([
                $mapping,
                ServiceLocatorTagPass::register($container, $refMap),
            ]);
    }

    /**
     * @param ContainerBuilder $container
     *
     * @return array
     */
    private function buildMappingAndRefMap(ContainerBuilder $container): array
    {
        $transformers = $container->findTaggedServiceIds($this->tag, true);
        $mapping = [];
        $refMap = [];

        foreach ($transformers as $id => $tags) {
            $type = $this->resolveType($tags, $id);
            $this->assertTypeNotDefined($type, $mapping);
            $mapping[$type] = $id;
            $refMap[$id] = new TypedReference($id, $container->getDefinition($id)->getClass());
        }

        return [ $mapping, $refMap ];
    }

    /**
     * @param array $tags
     * @param string $id
     *
     * @return string
     */
    private function resolveType(array $tags, string $id): string
    {
        $typeProperty = $this->typeProperty;

        if (empty($tags[0][$typeProperty])) {
            throw new InvalidArgumentException(
                "The `$typeProperty` property is required for service `$id`."
            );
        }

        return $tags[0][$typeProperty];
    }

    /**
     * @param string $type
     * @param array $mapping
     */
    private function assertTypeNotDefined(string $type, array $mapping)
    {
        if (isset($mapping[$type])) {
            throw new LogicException(
                "The type `$type` already has a transformation: `{$mapping[$type]}`."
            );
        }
    }
}
