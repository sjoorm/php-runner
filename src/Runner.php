<?php

/*
 * This file is part of the andreas-weber/php-runner library.
 *
 * (c) Andreas Weber <code@andreas-weber.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AndreasWeber\Runner;

use AndreasWeber\Runner\Payload\PayloadInterface;
use AndreasWeber\Runner\TaskSet\TaskSet;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;

class Runner implements LoggerAwareInterface
{
    public function __construct(TaskSet $taskSet = null, LoggerInterface $logger = null)
    {

    }

    public function setTaskSet(TaskSet $taskSet)
    {

    }

    public function getTaskSet()
    {

    }

    public function run(PayloadInterface $payload)
    {

    }

    public function onSuccess(callable $callable)
    {

    }

    public function onFailure(callable $callable)
    {

    }

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     * @return null
     */
    public function setLogger(LoggerInterface $logger)
    {
        // TODO: Implement setLogger() method.
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        // todo: look if logger is set
    }
}
