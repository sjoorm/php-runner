<?php

/*
 * This file is part of the andreas-weber/php-runner library.
 *
 * (c) Andreas Weber <code@andreas-weber.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AndreasWeber\Runner\Test\Task;

use AndreasWeber\Runner\Task\Collection;
use AndreasWeber\Runner\Task\TaskInterface;
use AndreasWeber\Runner\Test\Task\Stub\TaskStub;

class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Collection
     */
    private $collection;

    /**
     * @var TaskInterface
     */
    private $task;

    protected function setUp()
    {
        $this->collection = new Collection();
        $this->task = new TaskStub();
    }

    public function testGetTasksReturnsEmptyArrayWhenNoTaskIsSet()
    {
        $collection = $this->collection;
        $tasks = $collection->getTasks();

        $this->assertInstanceOf('\SplObjectStorage', $tasks);
        $this->assertCount(0, $tasks);
    }

    public function testAddTask()
    {
        $collection = $this->collection;

        $collection->addTask($this->task);
        $this->assertCount(1, $collection->getTasks());
    }

    public function testRemoveTask()
    {
        $collection = $this->collection;

        $collection->addTask($this->task);
        $collection->removeTask($this->task);

        $this->assertCount(0, $collection->getTasks());
    }

    public function testAddTasks()
    {
        $collection = $this->collection;

        $collection->addTasks(array($this->task));
        $this->assertCount(1, $collection->getTasks());
    }

    public function testAddTasksByConstructor()
    {
        $collection = new Collection(array($this->task));

        $this->assertCount(1, $collection->getTasks());
    }
}
