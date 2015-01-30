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

class SetUpTearDownCalledTaskStub extends AbstractTask
{
    /**
     * @var bool
     */
    private $setUpCalled = false;

    /**
     * @var bool
     */
    private $tearDownCalled = false;

    public function setUp()
    {
        $this->setUpCalled = true;
    }

    public function isSetUpCalled()
    {
        return $this->setUpCalled;
    }

    public function tearDown()
    {
        $this->tearDownCalled = true;
    }

    public function isTearDownCalled()
    {
        return $this->tearDownCalled;
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
        return null;
    }
}
