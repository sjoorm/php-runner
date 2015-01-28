<?php

/*
 * This file is part of the andreas-weber/php-runner library.
 *
 * (c) Andreas Weber <code@andreas-weber.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AndreasWeber\Runner\Event;

use AndreasWeber\Runner\Payload\PayloadInterface;

interface EventInterface
{
    public function __invoke(PayloadInterface $payload);
}
