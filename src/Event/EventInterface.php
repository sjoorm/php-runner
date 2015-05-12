<?php

/*
 * This file is part of the andreas-weber/php-runner library.
 *
 * (c) Andreas Weber <code@andreas-weber.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AndreasWeber\Runner\Event;

use AndreasWeber\Runner\Payload\PayloadInterface;
use AndreasWeber\Runner\Task\TaskInterface;

interface EventInterface
{
    /**
     * Sets the task.
     *
     * @param TaskInterface $task
     *
     * @return $this
     */
    public function setTask(TaskInterface $task);

    /**
     * Returns the task
     *
     * @return TaskInterface
     */
    public function getTask();

    /**
     * Sets the payload.
     *
     * @param PayloadInterface $payload
     *
     * @return $this
     */
    public function setPayload(PayloadInterface $payload);

    /**
     * Returns the payload.
     *
     * @return PayloadInterface
     */
    public function getPayload();

    /**
     * Sets the exit code.
     *
     * @param int $exitCode
     *
     * @return $this
     */
    public function setExitCode($exitCode);

    /**
     * Returns the exit code.
     *
     * @return int
     */
    public function getExitCode();
}
