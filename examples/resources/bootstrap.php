<?php

/*
 * This file is part of the andreas-weber/php-runner library.
 *
 * (c) Andreas Weber <code@andreas-weber.me>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

use Monolog\Logger;

define('BASEPATH', __DIR__ . '/../../');

require_once BASEPATH . '/vendor/autoload.php';
require_once BASEPATH . '/examples/resources/Task1.php';
require_once BASEPATH . '/examples/resources/Task2.php';
require_once BASEPATH . '/examples/resources/TaskExitCode.php';
require_once BASEPATH . '/examples/resources/TaskFail.php';
require_once BASEPATH . '/examples/resources/TaskUnless.php';
require_once BASEPATH . '/examples/resources/TaskSetUp.php';

$logger = new Logger('example-logger');
