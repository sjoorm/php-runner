<?php

/*
 * This file is part of the andreas-weber/php-runner library.
 *
 * (c) Andreas Weber <code@andreas-weber.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AndreasWeber\Runner\Test\Task\Stub;

use AndreasWeber\Runner\Payload\PayloadInterface;
use AndreasWeber\Runner\Task\AbstractTask;

class ExitCodeOneStub extends AbstractTask
{
    /**
     * Executes the task.
     *
     * @param PayloadInterface $payload
     *
     * @return null|int The exit status code
     */
    public function run(PayloadInterface $payload)
    {
        return 1;
    }
}
