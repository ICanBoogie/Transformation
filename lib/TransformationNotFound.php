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
 * Exception throws when a transformation cannot be found to transform data.
 */
class TransformationNotFound extends \LogicException implements Exception
{
    /**
     * @var mixed
     */
    private $data;

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param mixed $data
     * @param string|null $message
     * @param \Throwable|null $previous
     */
    public function __construct($data, string $message = null, \Throwable $previous = null)
    {
        $this->data = $data;

        parent::__construct($message ?: $this->formatMessage($data), 0, $previous);
    }

    /**
     * @param mixed $data
     *
     * @return string
     */
    private function formatMessage($data): string
    {
        $type = is_object($data) ? "instance of " . get_class($data) : "type " . gettype($data);

        return "Transformation not found for $type.";
    }
}
