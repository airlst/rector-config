<?php

declare(strict_types=1);

$factory = new Airlst\PhpstanConfig\Factory(['src']);

return $factory->level(9)->withBleedingEdge()->create();
