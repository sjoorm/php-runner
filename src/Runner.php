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

use AndreasWeber\Runner\Exception\LogicException;
use AndreasWeber\Runner\Payload\PayloadInterface;
use AndreasWeber\Runner\Runner\Exception\RunFailedException;
use AndreasWeber\Runner\Task\Collection;
use AndreasWeber\Runner\Task\Exception\FailException;
use AndreasWeber\Runner\Task\Exception\RetryException;
use AndreasWeber\Runner\Task\Exception\SkipException;
use AndreasWeber\Runner\Task\TaskInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

class Runner implements LoggerAwareInterface
{
    /**
     * @var Collection Task collection
     */
    private $taskCollection;

    /**
     * @var LoggerInterface Logger
     */
    private $logger;

    /**
     * @var callable Success callback
     */
    private $onSuccessCallback;

    /**
     * @var callable Failure callback
     */
    private $onFailureCallback;

    /**
     * @var \SplObjectStorage Attached runners
     */
    private $runners;

    /**
     * __construct()
     *
     * @param Collection $taskCollection
     * @param LoggerInterface $logger
     */
    public function __construct(Collection $taskCollection = null, LoggerInterface $logger = null)
    {
        $this->taskCollection = $taskCollection;
        $this->logger = $logger;
        $this->runners = new \SplObjectStorage();
    }

    /**
     * Sets the task collection.
     *
     * @param Collection $taskCollection
     *
     * @return $this
     */
    public function setTaskCollection(Collection $taskCollection)
    {
        $this->taskCollection = $taskCollection;

        return $this;
    }

    /**
     * Gets the task collection.
     *
     * @return Collection
     */
    public function getTaskCollection()
    {
        if (!$this->taskCollection) {
            throw new LogicException('Can\'t get task collection. None set.');
        }

        return $this->taskCollection;
    }

    /**
     * Invokes the task collection execution.
     * The payload will be passed from task to task.
     *
     * @param PayloadInterface $payload
     *
     * @return PayloadInterface
     */
    public function run(PayloadInterface $payload)
    {
        $tasks = $this->getTaskCollection()->getTasks();
        $tasksCount = $tasks->count();

        if (0 === $tasksCount) {
            throw new LogicException('Can\'t invoke task run. Empty task collection set.');
        }

        $this->log(LogLevel::INFO, sprintf('Starting runner with %s tasks ready for execution.', $tasksCount));

        foreach ($tasks as $task) {
            try {
                /** @var TaskInterface $task */
                $task->setPayload($payload);
                $this->runTask($task, $payload);
            } catch (\Exception $e) {
                $this->logTask(
                    $task,
                    LogLevel::ERROR,
                    sprintf('An exception was thrown. Message: %s', $e->getMessage())
                );
                $this->callOnFailureCallback($payload);
                throw new RunFailedException(
                    'Complete run failed: ' . $e->getMessage(),
                    $e->getCode(),
                    $e
                );
            }
        }

        $this->log(LogLevel::INFO, 'All tasks were processed.');
        $this->callOnSuccessCallback($payload);

        $this->log(LogLevel::INFO, 'Calling attached runners.');
        $this->notify($payload);

        return $payload;
    }

    /**
     * Invokes the task execution.
     *
     * @param TaskInterface $task
     * @param PayloadInterface $payload
     *
     * @return null
     */
    private function runTask(TaskInterface $task, PayloadInterface $payload)
    {
        $this->logTask($task, LogLevel::INFO, 'Starting execution.');

        try {
            if (!$task->unless()) {
                $this->logTask($task, LogLevel::INFO, 'Skipping because unless() returned boolean false.');
                return;
            }

            $task->setUp();
            $task->run($payload);
            $task->tearDown();
            $task->markAsSuccessfullyExecuted();
        } catch (SkipException $e) {
            $this->logTask($task, LogLevel::INFO, 'Skipping.');
        } catch (RetryException $e) {
            $this->logTask($task, LogLevel::NOTICE, 'Retry thrown. Starting again.');
            if (!$task->getMaxRetries()) {
                throw new LogicException('A retry exception was thrown, but no retries instance was set.');
            }
            $task->getMaxRetries()->increase();
            $this->runTask($task, $payload);
            return;
        } catch (FailException $e) {
            $this->logTask($task, LogLevel::INFO, 'Failure thrown.');
            throw $e;
        }

        $this->logTask($task, LogLevel::INFO, 'Execution successful.');
    }

    /**
     * Invokes success callback.
     *
     * @param PayloadInterface $payload
     *
     * @return null
     */
    private function callOnSuccessCallback(PayloadInterface $payload)
    {
        $this->log(LogLevel::INFO, 'Invoking success callback.');

        if ($this->onSuccessCallback) {
            call_user_func($this->onSuccessCallback, $payload);
        }
    }

    /**
     * Invokes failure callback.
     *
     * @param PayloadInterface $payload
     *
     * @return null
     */
    private function callOnFailureCallback(PayloadInterface $payload)
    {
        $this->log(LogLevel::INFO, 'Invoking failure callback.');

        if ($this->onFailureCallback) {
            call_user_func($this->onFailureCallback, $payload);
        }
    }

    /**
     * Success-Callback.
     * Callback will be invoked, when run was successful.
     *
     * @param callable $callable The callable to execute
     *
     * @return $this
     */
    public function onSuccess(callable $callable)
    {
        $this->onSuccessCallback = $callable;

        return $this;
    }

    /**
     * Failure-Callback.
     * Callback will be invoked, when run failed with errors.
     *
     * @param callable $callable The callable to execute
     *
     * @return $this
     */
    public function onFailure(callable $callable)
    {
        $this->onFailureCallback = $callable;

        return $this;
    }

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger
     *
     * @return $this
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;

        return $this;
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
    protected function log($level, $message, array $context = array())
    {
        if ($this->logger) {
            $this->logger->log($level, $message, $context);
        }
    }

    /**
     * Logs with an arbitrary level.
     * Specialized to pass a task instance.
     *
     * @param TaskInterface $task
     * @param mixed $level
     * @param string $message
     * @param array $context
     *
     * @return null
     */
    protected function logTask(TaskInterface $task, $level, $message, array $context = array())
    {
        $class = get_class($task);
        $message = sprintf('Task: %s. ', $class) . $message;

        $this->log($level, $message, $context);
    }

    /**
     * Notifies all attached runners to start execution with passed payload.
     *
     * @param PayloadInterface $payload
     *
     * @return $this
     */
    public function notify(PayloadInterface $payload)
    {
        foreach ($this->runners as $runner) {
            /** @var Runner $runner */
            $runner->run($payload);
        }

        return $this;
    }

    /**
     * Attaches a runner.
     *
     * @param Runner $runner
     *
     * @return $this
     */
    public function attach(Runner $runner)
    {
        if ($this->runners->contains($runner)) {
            throw new LogicException('Can\'t attach already attached runner.');
        }

        $this->runners->attach($runner);

        return $this;
    }

    /**
     * Detaches a runner.
     *
     * @param Runner $runner
     *
     * @return $this
     */
    public function detach(Runner $runner)
    {
        if (!$this->runners->contains($runner)) {
            throw new LogicException('Can\'t detach not attached runner.');
        }

        $this->runners->detach($runner);

        return $this;
    }
}
