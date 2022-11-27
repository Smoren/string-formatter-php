# string-formatter

![Packagist PHP Version Support](https://img.shields.io/packagist/php-v/smoren/string-formatter)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/Smoren/string-formatter-php/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/Smoren/string-formatter-php/?branch=master)
[![Coverage Status](https://coveralls.io/repos/github/Smoren/string-formatter-php/badge.svg?branch=master)](https://coveralls.io/github/Smoren/string-formatter-php?branch=master)
![Build and test](https://github.com/Smoren/string-formatter-php/actions/workflows/test_master.yml/badge.svg)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

Helper for formatting strings with dynamic data

### How to install to your project
```
composer require smoren/string-formatter
```

### Unit testing
```
composer install
./vendor/bin/codecept build
./vendor/bin/codecept run unit tests/unit
```

### Usage

#### Basic usage

```php
use Smoren\StringFormatter\StringFormatter;

$input = 'Hello, {name}! Your position is {position}.';
$params = ['name' => 'Anna', 'position' => 'programmer'];
$result = StringFormatter::format($input, $params);
echo $result; // Hello, Anna! Your position is programmer.
```

#### Custom regexp usage

```php
use Smoren\StringFormatter\StringFormatter;

$input = 'Hello, %name%! Your position is %position%.';
$params = ['name' => 'Anna', 'position' => 'programmer'];
$result = StringFormatter::format($input, $params, false, '/%([a-z]+)%/');
echo $result; // Hello, Anna! Your position is programmer.
```

#### Errors handling

```php
use Smoren\StringFormatter\StringFormatter;
use Smoren\StringFormatter\StringFormatterException;

// Explicit mode
$input = 'Hello, {name}! Your position is {position}.';
$params = ['name' => 'Anna'];
try {
    StringFormatter::format($input, $params);
} catch(StringFormatterException $e) {
    print_r($e->getData()); // ['position']
}

// Silent mode
$input = 'Hello, {name}! Your position is {position}.';
$params = ['name' => 'Anna'];
$result = StringFormatter::format($input, $params, true);
echo $result; // Hello, Anna! Your position is {position}.

// Another variant of silent mode
$input = 'Hello, {name}! Your position is {position}.';
$params = ['name' => 'Anna'];
$result = StringFormatter::formatSilent($input, $params);
echo $result; // Hello, Anna! Your position is {position}.
```