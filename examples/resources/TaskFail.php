<?php

/*
 * This file is part of the andreas-weber/php-runner library.
 *
 * (c) Andreas Weber <code@andreas-weber.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AndreasWeber\Runner\Example;

use AndreasWeber\Runner\Payload\ArrayPayload;
use AndreasWeber\Runner\Payload\PayloadInterface;
use AndreasWeber\Runner\Task\AbstractTask;

class TaskFail extends AbstractTask
{
    /**
     * @var int Run counter
     */
    private $runCounter;

    /**
     * @var int Fail count
     */
    private $failCount;

    /**
     * __construct()
     *
     * @param int $failCount
     */
    public function __construct($failCount)
    {
        parent::__construct();

        $this->failCount = (int)$failCount;
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

        if ($this->runCounter <= $this->failCount) {
            // 2 retries triggered by method
            $this->retry();
        }

        /** @var ArrayPayload $payload */
        $payload->setData(
            array_merge(
                $payload->getData(),
                array(
                    __CLASS__ => true
                )
            )
        );
    }
}
