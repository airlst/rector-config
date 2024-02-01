<?php

declare(strict_types=1);

$factory = new Airlst\PhpCsFixerConfig\Factory(['src']);

return $factory->php82()->create();