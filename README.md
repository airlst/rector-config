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

use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $factory = new Airlst\RectorConfig\Factory(['src']);
    $factory->create($rectorConfig);
};
```

The constructor of the `Factory` class takes an array of paths to be scanned for PHP files and fixed. You can pass any number of paths to it.

### PHP 8.2 support

By default, it uses PHP 8.3 as the target version. You can switch to PHP 8.2 by calling the `php82()` method on the factory object:

```php
<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $factory = new Airlst\RectorConfig\Factory(['src']);
    $factory->php82()->create($rectorConfig);
};
```

Only PHP 8.2 and 8.3 are supported.

### Using cache for Rector

By default, Rector uses in-memory cache.
You can use file cache for Rector by calling the `useFileCache()` method on the factory object and providing the path to the cache directory:

```php
<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $factory = new Airlst\RectorConfig\Factory(['src']);
    $factory->php82()->useFileCache('cache/rector')->create($rectorConfig);
};
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
