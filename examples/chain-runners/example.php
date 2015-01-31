<?php

/*
 * This file is part of the andreas-weber/php-runner library.
 *
 * (c) Andreas Weber <code@andreas-weber.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use AndreasWeber\Runner\Example\Task1;
use AndreasWeber\Runner\Example\Task2;
use AndreasWeber\Runner\Payload\ArrayPayload;
use AndreasWeber\Runner\Runner;
use AndreasWeber\Runner\Task\Collection;

require_once __DIR__ . '/../resources/bootstrap.php';

//
// Runner 1
//

$collection1 = new Collection();
$collection1->addTask(new Task1());
$collection1->addTask(new Task2());

$runner1 = new Runner($collection1, $logger);

//
// Runner 2
//

$collection2 = new Collection();
$collection2->addTask(new Task1());
$collection2->addTask(new Task2());

$runner2 = new Runner($collection2, $logger);

//
// Chaining
//

$runner1->attach($runner2);

//
// Invoke execution
//

$payload = new ArrayPayload();
$runner1->run($payload);

// dump payload
var_export($payload);
