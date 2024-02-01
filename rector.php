<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;

return static function (RectorConfig $rectorConfig): void {
    $factory = new Airlst\RectorConfig\Factory(['src']);

    $factory->php82()->create($rectorConfig);
};
