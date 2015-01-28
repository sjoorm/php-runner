<?php

/*
 * This file is part of the andreas-weber/php-runner library.
 *
 * (c) Andreas Weber <code@andreas-weber.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AndreasWeber\Runner\Test\Payload;

use AndreasWeber\Runner\Payload\ArrayPayload;

class ArrayPayloadTest extends \PHPUnit_Framework_TestCase
{
    public function testSetDataIsSuccessful()
    {
        $data = array(
            'foo' => 'bar'
        );

        $payload = new ArrayPayload();
        $payload->setData($data);

        $this->assertEquals(
            $data,
            $payload->getData()
        );
    }

    public function testGetDataIsSuccessful()
    {
        $payload = new ArrayPayload();

        $this->assertEmpty(
            $payload->getData()
        );
    }

    public function testSetDataByConstructorIsSuccessful()
    {
        $data = array(
            'foo' => 'bar'
        );

        $payload = new ArrayPayload($data);

        $this->assertEquals(
            $data,
            $payload->getData()
        );
    }

    public function testSetDataIsFluent()
    {
        $payload = new ArrayPayload();

        $this->assertSame(
            $payload,
            $payload->setData(array())
        );
    }
}
