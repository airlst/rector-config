# Rector config for AirLST projects

[![Latest Version on Packagist](https://img.shields.io/packagist/v/airlst/rector-config.svg?style=flat-square)](https://packagist.org/packages/airlst/rector-config)
[![Total Downloads](https://img.shields.io/packagist/dt/airlst/rector-config.svg?style=flat-square)](https://packagist.org/packages/airlst/rector-config)

Rector config for AirLST projects.

## Installation

You can install the package via Composer:

```bash
composer require --dev airlst/rector-config
```

## Usage

Create a `rector.php` in the root of your project with the following contents:

```php
<?php

declare(strict_types=1);

$factory = new Airlst\RectorConfig\Factory(['src']);

return $factory->create();
```

The constructor of the `Factory` class takes an array of paths to be scanned for PHP files and fixed. You can pass any number of paths to it.

The method returns an instance of `Rector\Configuration\RectorConfigBuilder` which can be further configured.
For example, you can instruct Rector to use file cache:

```php
<?php

declare(strict_types=1);

$factory = new Airlst\RectorConfig\Factory(['src']);

return $factory
    ->create()
    ->withCache('cache/rector');
```

### Running Rector

Run Rector with the following command:

```shell
./vendor/bin/rector
```

### Skipping rules

You can skip certain rules by chaining the `withSkip()` method before calling `create()`:

```php
<?php

declare(strict_types=1);

$factory = new Airlst\RectorConfig\Factory(['src']);

return $factory
    ->withSkip([
        Rector\DeadCode\Rector\PropertyProperty\RemoveNullPropertyInitializationRector::class,
    ])
    ->create();
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
