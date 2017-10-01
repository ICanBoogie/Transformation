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

/**
 * An abstract transformation provider.
 */
abstract class TransformationProviderAbstract implements TransformationProvider
{
    /**
     * @var array
     */
    private $transformations;

    /**
     * @param array $transformations An array of key/value pairs where _key_ is a supported class and _value_
     * a transformation. What qualifies as _transformation_ depends on the implementation. It could be a
     * Transformation instance, a callable, or a reference to a service…
     */
    public function __construct(array $transformations)
    {
        $this->transformations = $transformations;
    }

    /**
     * @inheritdoc
     */
    public function __invoke($data): callable
    {
        foreach ($this->transformations as $type => $transformation) {
            if (!$data instanceof $type) {
                continue;
            }

            try {
                return $this->resolve($transformation);
            } catch (\Throwable $e) {
                throw new TransformationNotFound($data, null, $e);
            }
        }

        throw new TransformationNotFound($data);
    }

    /**
     * Resolves a transformation.
     *
     * @param mixed $transformation
     *
     * @return Transformation|callable
     */
    abstract protected function resolve($transformation): callable;
}
