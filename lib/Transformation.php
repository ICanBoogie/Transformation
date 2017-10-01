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
 * A recommended interface for transformations.
 *
 * Although this interface if provided, transformations don't have to use it, simple callables will do.
 */
interface Transformation
{
    /**
     * @param mixed $data
     * @param Transformation|callable|null $transformation A transformation for nested transformations.
     *
     * @return mixed
     */
    public function __invoke($data, callable $transformation = null);
}
