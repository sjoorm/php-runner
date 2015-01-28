<?php

/*
 * This file is part of the andreas-weber/php-runner library.
 *
 * (c) Andreas Weber <code@andreas-weber.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AndreasWeber\Runner\Task;

abstract class AbstractTask implements TaskInterface
{
    public function unless()
    {

    }

    public function setUp()
    {

    }

    public function tearDown()
    {

    }

    protected function skip()
    {

    }

    protected function fail()
    {

    }

    protected function retry()
    {

    }

    public function __clone()
    {
        // not allowed exception
    }
}
