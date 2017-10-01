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
 * A transformation context.
 */
class Context implements \ArrayAccess
{
    /**
     * @var array
     */
    private $data = [];

    /**
     * @var array
     */
    private $chain = [];

    /**
     * @param array $data
     */
    public function __construct(array $data = [])
    {
        $this->data = $data;
    }

    /**
     * @inheritdoc
     */
    public function offsetExists($offset)
    {
        return isset($this->data[$offset]);
    }

    /**
     * @inheritdoc
     */
    public function offsetGet($offset)
    {
        return $this->data[$offset] ?? null;
    }

    /**
     * @inheritdoc
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * @inheritdoc
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * Push data to the transformation chain.
     *
     * @param mixed $data
     */
    public function push($data)
    {
        array_push($this->chain, $data);
    }

    /**
     * Pop data from the transformation chain.
     */
    public function pop()
    {
        array_pop($this->chain);
    }

    /**
     * Whether the current data being transformed is the root data.
     *
     * @return bool
     */
    public function isRoot(): bool
    {
        return count($this->chain) < 2;
    }

    /**
     * Whether the current data being transformed is a branch.
     *
     * @return bool
     */
    public function isBranch(): bool
    {
        return count($this->chain) > 1;
    }

    /**
     * Finds the closest item in the transformation chain matching the specified class.
     *
     * @param string $class
     *
     * @return mixed|null
     */
    public function closest(string $class)
    {
        foreach (array_reverse($this->chain) as $data) {
            if ($data instanceof $class) {
                return $data;
            }
        }

        return null;
    }
}
