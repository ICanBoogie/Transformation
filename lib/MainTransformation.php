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

        try {
            return ($this->transformationProvider)($data)($data, $transformation);
        } catch (TransformationNotFound $e) {
            // it's ok, we might still be able to transform it…
        }

        if (is_iterable($data)) {
            $array = [];

            foreach ($data as $item) {
                $array[] = $this($item, $transformation);
            }

            return $array;
        }

        throw new TransformationNotFound($data, null, $e);
    }
}
