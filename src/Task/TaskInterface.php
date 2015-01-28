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

use AndreasWeber\Runner\Payload\PayloadInterface;

interface TaskInterface
{
    public function setPayload(PayloadInterface $payload);
    public function getPayload();

    public function setMaxRetries($retries); // cast internal to retries instance
    public function getMaxRetries();

    public function run(PayloadInterface $payload);

    public function markAsCleanupTask();
}
