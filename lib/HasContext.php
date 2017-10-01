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
 * This interface is implemented by transformations that provide a context.
 */
interface HasContext
{
    /**
     * @return Context
     */
    public function getContext(): Context;
}
