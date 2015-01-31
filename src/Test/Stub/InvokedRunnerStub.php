<?php

/*
 * This file is part of the andreas-weber/php-runner library.
 *
 * (c) Andreas Weber <code@andreas-weber.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AndreasWeber\Runner\Test\Stub;

use AndreasWeber\Runner\Payload\PayloadInterface;
use AndreasWeber\Runner\Runner;

class InvokedRunnerStub extends Runner
{
    private $invoked = false;

    public function run(PayloadInterface $payload)
    {
        $this->invoked = true;

        return parent::run($payload);
    }

    public function isInvoked()
    {
        return $this->invoked;
    }
}
