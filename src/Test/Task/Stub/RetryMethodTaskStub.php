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

class RetryMethodTaskStub extends RunCounterTaskStub
{
    /**
     * @var int Retries to perform
     */
    private $retries;

    public function __construct($retries = 2)
    {
        parent::__construct();

        \Assert\that($retries)->integer()->min(1);

        $this->retries = $retries;
    }

    public function run(PayloadInterface $payload)
    {
        parent::run($payload);

        if ($this->getRunsCount() <= $this->retries) {
            // 2 retries triggered by method
            $this->retry();
        }

        return null;
    }
}
