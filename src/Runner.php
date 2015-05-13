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

use AndreasWeber\Runner\Event\Event;
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
use Psr\Log\NullLogger;
use Symfony\Component\EventDispatcher\EventDispatcher;

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
     * @var \SplObjectStorage Attached runners
     */
    private $runners;

    /**
     * @var EventDispatcher Event dispatcher
     */
    private $eventDispatcher;

    /**
     * __construct()
     *
     * @param Collection $taskCollection
     */
    public function __construct(Collection $taskCollection = null)
    {
        $this->taskCollection = $taskCollection;
        $this->logger = new NullLogger();
        $this->runners = new \SplObjectStorage();
        $this->eventDispatcher = new EventDispatcher();
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
        $this->dispatch('runner.start', null, $payload);

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
                $this->dispatch('runner.failure', null, null, null, $e);
                throw new RunFailedException(
                    'Complete run failed: ' . $e->getMessage(),
                    $e->getCode(),
                    $e
                );
            }
        }

        $this->log(LogLevel::INFO, 'All tasks were processed.');
        $this->log(LogLevel::INFO, 'Calling attached runners.');
        $this->notify($payload);

        $this->log(LogLevel::INFO, 'Execution successful.');
        $this->dispatch('runner.success', null, $payload);

        return $payload;
    }

    /**
     * Invokes the task execution.
     *
     * @param TaskInterface    $task
     * @param PayloadInterface $payload
     *
     * @return null
     */
    private function runTask(TaskInterface $task, PayloadInterface $payload)
    {
        $this->logTask($task, LogLevel::INFO, 'Starting execution.');

        try {
            if (!$task->unless()) {
                $this->dispatch('runner.task.unless', $task, $payload);
                $this->logTask($task, LogLevel::INFO, 'Skipping because unless() returned boolean false.');
                return;
            }

            $this->dispatch('runner.task.start', $task, $payload);

            $task->setUp();
            $exitCode = (int)$task->run($payload) ?: 0;
            $task->tearDown();

            if ($task->isFailOnError() && $exitCode !== 0) {
                throw new FailException(
                    sprintf(
                        'Task: %s failed with exit code %s',
                        get_class($task),
                        $exitCode
                    )
                );
            }

            $message = sprintf('Task exited with status code %s', $exitCode);
            if ($exitCode === 0) {
                $this->logTask($task, LogLevel::INFO, $message);
            } else {
                $this->logTask($task, LogLevel::WARNING, $message);
            }

            $this->dispatch('runner.task.success', $task, $payload, $exitCode);
            $task->markAsSuccessfullyExecuted();
        } catch (SkipException $e) {
            $this->logTask($task, LogLevel::INFO, 'Skipping.');
            $this->dispatch('runner.task.skip', $task, $payload);
        } catch (RetryException $e) {
            $this->logTask($task, LogLevel::NOTICE, 'Retry thrown. Starting again.');
            $this->dispatch('runner.task.retry', $task, $payload);
            if (!$task->getMaxRetries()) {
                throw new LogicException('A retry exception was thrown, but no retries instance was set.');
            }
            $task->getMaxRetries()->increase();
            $this->runTask($task, $payload);
            return;
        } catch (FailException $e) {
            $this->logTask(
                $task,
                LogLevel::WARNING,
                sprintf(
                    'Failure thrown. Given message: %s',
                    $e->getMessage()
                )
            );

            $exitCode = $e->getCode();
            if (is_int($exitCode)) {
                $this->dispatch('runner.task.failure', $task, $payload, $exitCode);
            } else {
                $this->dispatch('runner.task.failure', $task, $payload);
            }

            throw $e;
        }

        $this->logTask($task, LogLevel::INFO, 'Execution successful.');
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
     * @param mixed  $level
     * @param string $message
     * @param array  $context
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
     * @param mixed         $level
     * @param string        $message
     * @param array         $context
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

    /**
     * Register an callable to an event.
     *
     * @param string   $eventName
     * @param callable $callable
     *
     * @return $this
     */
    public function on($eventName, callable $callable)
    {
        \Assert\that($eventName)->string()->notEmpty();

        $this->eventDispatcher->addListener($eventName, $callable);

        return $this;
    }

    /**
     * Dispatches an event.
     *
     * @param string                $eventName
     * @param null|TaskInterface    $task
     * @param null|PayloadInterface $payload
     * @param null|int              $exitCode
     * @param null|\Exception       $exception
     *
     * @return null
     */
    private function dispatch(
        $eventName,
        TaskInterface $task = null,
        PayloadInterface $payload = null,
        $exitCode = null,
        \Exception $exception = null
    ) {
        \Assert\that($eventName)->string()->notEmpty();

        $event = new Event();
        $event->setRunner($this);

        if ($task) {
            $event->setTask($task);
        }

        if ($payload) {
            $event->setPayload($payload);
        }

        if (!is_null($exitCode)) {
            $event->setExitCode($exitCode);
        }

        if ($exception) {
            $event->setException($exception);
        }

        $this->logger->debug(
            sprintf(
                "Dispatching event '%s'",
                $eventName
            )
        );

        $this->eventDispatcher->dispatch($eventName, $event);
    }
}
