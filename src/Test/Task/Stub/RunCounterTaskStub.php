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

class RunCounterTaskStub extends AbstractTask
{
    /**
     * @var int Run counter
     */
    private $runCounter;

    /**
     * __construct()
     */
    public function __construct()
    {
        parent::__construct();

        $this->runCounter = 0;
    }

    /**
     * Executes the task.
     *
     * @param PayloadInterface $payload
     *
     * @return null
     */
    public function run(PayloadInterface $payload)
    {
        $this->runCounter++;

        return null;
    }

    /**
     * Returns the run count.
     *
     * @return int
     */
    public function getRunsCount()
    {
        return $this->runCounter;
    }
}
