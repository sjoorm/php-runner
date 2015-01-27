# andreas-weber/php-runner

Library to simplify the implementation of sophisticated interdependent tasks.

## Requirements
Check shipped composer.json.

## Installation

Simply add a dependency on `andreas-weber/php-runner` to your project's [Composer](http://getcomposer.org/) `composer.json` file.

## Usage

tbd..

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
