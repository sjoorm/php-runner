# andreas-weber/php-runner

[![Build Status](https://travis-ci.org/andreas-weber/php-runner.svg?branch=master)](https://travis-ci.org/andreas-weber/php-runner)

Library to simplify the implementation of sophisticated interdependent tasks.

## Features

- Encapsulate logic in different tasks instead of writing spaghetti code.
- Chain task runners to describe dependencies between tasks.
- Pass a payload to task runner. Payload gets passed from task to task.
- Skip single tasks during runtime by implementing unless() method.
- Use setUp() and tearDown() on each task to prepare and cleanup task execution.
- Use onSuccess() and onFailure() to attach callbacks on task runner.
- Call retry() in task to use task runners retry handling.
- Call skip() in task to skip task processing.
- Call fail() in task to fail complete task run.

## Requirements
Check shipped composer.json.

## Installation

Simply add a dependency on `andreas-weber/php-runner` to your project's [Composer](http://getcomposer.org/) `composer.json` file.

## Examples

- [Simple Task-Runner](examples/simple/example.php)
- [Task-Runner with Retry-Handling](examples/retry/example.php)
- [Using success callback](examples/success-callback/example.php)

## Developer

### Environment

Boot:

```
vagrant up
```

Enter virtual machine:

```
vagrant ssh
```

Run tests:

```
cd /vagrant
vendor/bin/phpunit src/Test/
```

### Build targets

```
vagrant@andreas-weber:/vagrant$ ant
Buildfile: /vagrant/build.xml

help:
     [echo]
     [echo] The following commands are available:
     [echo]
     [echo] |   +++ Build +++
     [echo] |-- build                (Run the build)
     [echo] |   |-- dependencies     (Install dependencies)
     [echo] |   |-- tests            (Lint all files and run tests)
     [echo] |   |-- metrics          (Generate quality metrics)
     [echo] |-- cleanup              (Cleanup the build directory)
     [echo] |
     [echo] |   +++ Composer +++
     [echo] |-- composer             -> composer-download, composer-install
     [echo] |-- composer-download    (Downloads composer.phar to project)
     [echo] |-- composer-install     (Install all dependencies)
     [echo] |
     [echo] |   +++ Testing +++
     [echo] |-- phpunit              -> phpunit-full
     [echo] |-- phpunit-tests        (Run unit tests)
     [echo] |-- phpunit-full         (Run unit tests and generate code coverage report / logs)
     [echo] |
     [echo] |   +++ Metrics +++
     [echo] |-- coverage             (Show code coverage metric)
     [echo] |-- phploc               (Show lines of code metric)
     [echo] |-- qa                   (Run quality assurance tools)
     [echo] |-- |-- phpcpd           (Show copy paste metric)
     [echo] |-- |-- phpcs            (Show code sniffer metric)
     [echo] |-- |-- phpmd            (Show mess detector metric)
     [echo] |
     [echo] |   +++ Metric Reports +++
     [echo] |-- phploc-report        (Generate lines of code metric report)
     [echo] |-- phpcpd-report        (Generate copy paste metric report)
     [echo] |-- phpcs-report         (Generate code sniffer metric report)
     [echo] |-- phpmd-report         (Generate mess detector metric report)
     [echo] |
     [echo] |   +++ Tools +++
     [echo] |-- lint                 (Lint all php files)
     [echo]
```

## Thoughts
Pull requests are highly appreciated. Built with love. Hope you'll enjoy.. :-)
