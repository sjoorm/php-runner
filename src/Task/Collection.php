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

use AndreasWeber\Runner\Task\TaskInterface;

class Collection
{
    /**
     * @var \SplObjectStorage Tasks
     */
    private $tasks;

    /**
     * __construct()
     *
     * @param TaskInterface[] $tasks
     */
    public function __construct(array $tasks = null)
    {
        $this->tasks = new \SplObjectStorage();

        if ($tasks) {
            $this->addTasks($tasks);
        }
    }

    /**
     * Add tasks to collection.
     *
     * @param TaskInterface[] $tasks
     *
     * @return $this
     */
    public function addTasks(array $tasks)
    {
        foreach ($tasks as $task) {
            $this->addTask($task);
        }

        return $this;
    }

    /**
     * Add task to collection.
     *
     * @param TaskInterface $task
     *
     * @return $this
     */
    public function addTask(TaskInterface $task)
    {
        $this->tasks->attach($task);

        return $this;
    }

    /**
     * Remove task from collection.
     *
     * @param TaskInterface $task
     *
     * @return $this
     */
    public function removeTask(TaskInterface $task)
    {
        $this->tasks->detach($task);

        return $this;
    }

    /**
     * Return tasks.
     *
     * @return \SplObjectStorage
     */
    public function getTasks()
    {
        return $this->tasks;
    }
}
