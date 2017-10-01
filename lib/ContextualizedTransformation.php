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
 * A transformation associated with a context.
 *
 * Nested transformation can use the context the present the data differently. e.g. removing a number of fields if
 * they are embedded in a set, or because a user is lacking some permissions.
 */
class ContextualizedTransformation implements HasContext, Transformation
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var Transformation|callable
     */
    private $transformation;

    /**
     * @param Context|null $context
     * @param Transformation|callable|null $transformation
     */
    public function __construct(Context $context = null, callable $transformation = null)
    {
        $this->context = $context ?: new Context;
        $this->transformation = $transformation;
    }

    public function __clone()
    {
        $this->context = clone $this->context;
    }

    /**
     * @param mixed $data
     * @param Transformation|callable|null $transformation
     *
     * @return mixed
     */
    public function __invoke($data, callable $transformation = null)
    {
        if ($transformation) {
            $clone = clone $this;
            $clone->transformation = $transformation;

            return $clone($data);
        }

        $this->context->push($data);

        $result = ($this->transformation)($data, $this);

        $this->context->pop();

        return $result;
    }

    /**
     * @return Context
     */
    public function getContext(): Context
    {
        return $this->context;
    }
}
