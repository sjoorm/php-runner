<?php

/*
 * This file is part of the andreas-weber/php-runner library.
 *
 * (c) Andreas Weber <code@andreas-weber.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AndreasWeber\Runner\Task\Retries;

use AndreasWeber\Runner\Task\Retries\Exception\MaxRetriesExceededException;

class Retries
{
    /**
     * @var int Max allowed retries
     */
    private $maxRetries;

    /**
     * @var int Current retries
     */
    private $retries;

    /**
     * __construct()
     *
     * @param $maxRetries
     */
    public function __construct($maxRetries)
    {
        $this->maxRetries = (int)$maxRetries;
        $this->retries = 0;

        if ($this->maxRetries <= 0) {
            throw new \InvalidArgumentException('Max allowed retries count can\'t be negative or zero.');
        }
    }

    /**
     * Increases the retry counter by one.
     *
     * @return $this
     */
    public function increase()
    {
        $this->retries++;

        if ($this->retries > $this->maxRetries) {
            throw new MaxRetriesExceededException(
                sprintf(
                    'Max allowed retries exceeded. Allowed: %s. Tried: %s.',
                    $this->maxRetries,
                    $this->retries
                )
            );
        }

        return $this;
    }

    /**
     * Resets the retry counter.
     *
     * @return $this
     */
    public function reset()
    {
        $this->retries = 0;

        return $this;
    }

    /**
     * Returns the retry counter count.
     *
     * @return int
     */
    public function getRetries()
    {
        return $this->retries;
    }

    /**
     * Returns the max allowed retries.
     *
     * @return int
     */
    public function getMaxRetries()
    {
        return $this->maxRetries;
    }
}
