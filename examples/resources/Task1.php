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

class Task1 extends AbstractTask
{
    /**
     * Executes the task.
     *
     * @param PayloadInterface $payload
     *
     * @return null
     */
    public function run(PayloadInterface $payload)
    {
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
