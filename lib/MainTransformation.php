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
 * A transformation that uses a provider to find the best transformation for a given data.
 *
 * This transformation is very useful when dealing with nested data, or when you don't want to deal with type
 * checking.
 */
class MainTransformation implements Transformation
{
    /**
     * @var TransformationProvider|callable
     */
    private $transformationProvider;

    /**
     * @var Transformation[]
     */
    private $cache = [];

    /**
     * @param TransformationProvider|callable $transformationProvider
     */
    public function __construct(callable $transformationProvider)
    {
        $this->transformationProvider = $transformationProvider;
    }

    /**
     * @param mixed $data
     * @param Transformation|callable|null $transformation If provided, the specified transformation is used
     * instead of this one as parameter for the transformation associated with the data.
     *
     * @return mixed
     */
    public function __invoke($data, callable $transformation = null)
    {
        if ($data === null || is_scalar($data)) {
            return $data;
        }

        $transformation = $transformation ?: $this;

        if (is_object($data) && !$data instanceof \stdClass) {
            try {
                return $this->resolveTransformation($data)($data, $transformation);
            } catch (TransformationNotFound $e) {
                if (!$data instanceof \Traversable) {
                    throw $e;
                }

                // The Traversable does not have a transformation, let's iterate over it.
            }
        }

        return $this->transformIterable($data, $transformation);
    }

    /**
     * @param mixed $data
     *
     * @return callable
     */
    private function resolveTransformation($data): callable
    {
        $class = get_class($data);
        $transformation = &$this->cache[$class];

        return $transformation ?: $transformation = ($this->transformationProvider)($data);
    }

    /**
     * @param \Traversable|array $data
     * @param callable $transformation
     *
     * @return array
     */
    private function transformIterable($data, callable $transformation): array
    {
        $array = [];

        if ($data instanceof \stdClass) {
            foreach ($data as $key => $item) {
                $array[$key] = $this($item, $transformation);
            }
        } else {
            foreach ($data as $item) {
                $array[] = $this($item, $transformation);
            }
        }

        return $array;
    }
}
