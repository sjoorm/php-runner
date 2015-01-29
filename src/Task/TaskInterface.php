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
use AndreasWeber\Runner\Task\Retries\Retries;

interface TaskInterface
{
    /**
     * Sets the payload.
     *
     * @param PayloadInterface $payload
     *
     * @return $this
     */
    public function setPayload(PayloadInterface $payload);

    /**
     * Gets the payload.
     *
     * @return PayloadInterface
     */
    public function getPayload();

    /**
     * Sets the max allowed retries.
     *
     * @param int|Retries $retries
     *
     * @return $this
     */
    public function setMaxRetries($retries);

    /**
     * Gets the max allowed retries.
     *
     * @return Retries
     */
    public function getMaxRetries();

    /**
     * Executes the task.
     *
     * @param PayloadInterface $payload
     *
     * @return null
     */
    public function run(PayloadInterface $payload);

    /**
     * Marks the task as a cleanup task.
     * Cleanup tasks will always run, even if another previously task has failed.
     *
     * @return $this
     */
    public function markAsCleanupTask();

    /**
     * Returns boolean true, when task is marked as cleanup task.
     *
     * @return bool
     */
    public function isCleanupTask();
}
