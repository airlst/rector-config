<?php

declare(strict_types=1);

namespace Airlst\RectorConfig;

use Airlst\RectorConfig\Rector\IdenticalNullCheckToIsNullRector;
use Airlst\RectorConfig\Rector\UnderscoreToCamelCaseVariableNameRector;
use Rector\CodingStyle\Rector\Catch_\CatchExceptionNameMatchingTypeRector;
use Rector\CodingStyle\Rector\Encapsed\EncapsedStringsToSprintfRector;
use Rector\CodingStyle\Rector\Encapsed\WrapEncapsedVariableInCurlyBracesRector;
use Rector\CodingStyle\Rector\Stmt\NewlineAfterStatementRector;
use Rector\Config\RectorConfig;
use Rector\Configuration\RectorConfigBuilder;
use Rector\Php81\Rector\FuncCall\NullToStrictStringFuncCallArgRector;
use Rector\PHPUnit\Set\PHPUnitSetList;

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
            ->withSets([
                PHPUnitSetList::PHPUNIT_100,
                PHPUnitSetList::PHPUNIT_CODE_QUALITY,
            ])
            ->withRules([
                WrapEncapsedVariableInCurlyBracesRector::class,
                UnderscoreToCamelCaseVariableNameRector::class,
                IdenticalNullCheckToIsNullRector::class,
            ])
            ->withSkip([
                EncapsedStringsToSprintfRector::class,
                CatchExceptionNameMatchingTypeRector::class,
                NewlineAfterStatementRector::class,
                NullToStrictStringFuncCallArgRector::class,
                ...$this->skip,
            ])
            ->withSkipPath('_ide_helper*.php')
            ->withRootFiles();
    }
}
