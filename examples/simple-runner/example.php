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

define('BASEPATH', __DIR__ . '/../../');

require_once BASEPATH . '/vendor/autoload.php';

require_once BASEPATH . '/examples/resources/Task1.php';
require_once BASEPATH . '/examples/resources/Task2.php';

$collection = new Collection();
$collection->addTask(new Task1());
$collection->addTask(new Task2());

$payload = new ArrayPayload();
$runner = new Runner($collection);

$runner->run($payload);

// dump payload
var_export($payload);
