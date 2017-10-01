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

use ICanBoogie\Transformation\TransformationProviderAbstract;
use Psr\Container\ContainerInterface;

/**
 * Provides transformations from a container.
 */
class TransformationProvider extends TransformationProviderAbstract
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @inheritdoc
     * @param ContainerInterface $container
     */
    public function __construct(array $transformations, ContainerInterface $container)
    {
        $this->container = $container;

        parent::__construct($transformations);
    }

    /**
     * @inheritdoc
     */
    protected function resolve($transformation): callable
    {
        return $this->container->get($transformation);
    }
}
