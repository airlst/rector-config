<?php

declare(strict_types=1);

namespace Airlst\RectorConfig;

use Airlst\RectorConfig\Rector\UnderscoreToCamelCaseVariableNameRector;
use Rector\CodingStyle\Rector\ArrowFunction\StaticArrowFunctionRector;
use Rector\CodingStyle\Rector\Closure\StaticClosureRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\Encapsed\WrapEncapsedVariableInCurlyBracesRector;
use Rector\CodingStyle\Rector\PostInc\PostIncDecToPreIncDecRector;
use Rector\Config\RectorConfig;
use Rector\Configuration\RectorConfigBuilder;

class Factory
{
    /** @var array<string> */
    private array $skip = [];

    /** @param array<string> $withPaths */
    public function __construct(private readonly array $withPaths) {}

    /** @param array<string> $skip */
    public function withSkip(array $skip): self
    {
        $this->skip = $skip;

        return $this;
    }

    public function create(): RectorConfigBuilder
    {
        return RectorConfig::configure()
            ->withPaths($this->withPaths)
            ->withPreparedSets(
                deadCode: true,
                codeQuality: true,
                codingStyle: true,
                typeDeclarations: true,
                privatization: true,
                instanceOf: true,
                earlyReturn: true,
                strictBooleans: true
            )
            ->withAttributesSets(
                phpunit: true
            )
            ->withPhpSets()
            ->withRules([
                WrapEncapsedVariableInCurlyBracesRector::class,
                UnderscoreToCamelCaseVariableNameRector::class,
            ])
            ->withSkip([
                EncapsedStringsToSprintfRector::class,
                PostIncDecToPreIncDecRector::class,
                StaticClosureRector::class,
                StaticArrowFunctionRector::class,
                ...$this->skip,
            ])
            ->withRootFiles();
    }
}
