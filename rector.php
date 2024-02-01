<?php

declare(strict_types=1);

use Airlst\RectorConfig\Factory;
use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $factory = new Factory();
    $factory->directories(['src'])->phpVersion('8.2')->create($rectorConfig);
};
