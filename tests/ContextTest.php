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

use ICanBoogie\Transformation\TestCases\DataSample1;
use ICanBoogie\Transformation\TestCases\DataSample2;
use ICanBoogie\Transformation\TestCases\DataSample3;
use PHPUnit\Framework\TestCase;

class ContextTest extends TestCase
{
    /**
     * @test
     */
    public function shouldImplementArrayAccess()
    {
        $context = new Context([ ($key = uniqid()) => ($value = uniqid()) ]);
        $this->assertFalse(isset($context[$key2 = uniqid()]));
        $this->assertNull($context[$key2]);
        $this->assertSame($value, $context[$key]);
        unset($context[$key]);
        $this->assertNull($context[$key]);
        $context[$key] = $value;
        $this->assertSame($value, $context[$key]);
    }

    public function testIsRootIsBranch()
    {
        $context = new Context();
        $this->assertTrue($context->isRoot());
        $this->assertFalse($context->isBranch());
        $context->push(uniqid());
        $this->assertTrue($context->isRoot());
        $this->assertFalse($context->isBranch());
        $context->push(uniqid());
        $this->assertFalse($context->isRoot());
        $this->assertTrue($context->isBranch());
        $context->pop();
        $this->assertTrue($context->isRoot());
        $this->assertFalse($context->isBranch());
        $context->pop();
        $this->assertTrue($context->isRoot());
        $this->assertFalse($context->isBranch());
    }

    /**
     * @test
     */
    public function shouldReturnClosest()
    {
        $context = new Context();
        $context->push($data1 = new DataSample1);
        $context->push($data2 = new DataSample2);
        $context->push($data3 = new DataSample3);
        $data4 = new class {
        };

        $this->assertSame($data3, $context->closest(DataSample3::class));
        $this->assertSame($data2, $context->closest(DataSample2::class));
        $this->assertSame($data2, $context->closest(DataSample1::class));
        $this->assertNull($context->closest(get_class($data4)));
    }
}
