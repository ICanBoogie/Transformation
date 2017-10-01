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

interface TransformationProvider
{
    /**
     * @param mixed $data
     *
     * @return Transformation|callable
     *
     * @throws TransformationNotFound if no transformation can be found for the data.
     */
    public function __invoke($data): callable;
}
