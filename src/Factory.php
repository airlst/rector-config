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
use RectorLaravel\Rector\Class_\PropertyDeferToDeferrableProviderToRector;
use RectorLaravel\Rector\ClassMethod\AddParentBootToModelClassMethodRector;
use RectorLaravel\Rector\ClassMethod\AddParentRegisterToEventServiceProviderRector;
use RectorLaravel\Rector\Expr\SubStrToStartsWithOrEndsWithStaticMethodCallRector\SubStrToStartsWithOrEndsWithStaticMethodCallRector;
use RectorLaravel\Rector\FuncCall\FactoryFuncCallToStaticCallRector;
use RectorLaravel\Rector\MethodCall\AssertStatusToAssertMethodRector;
use RectorLaravel\Rector\MethodCall\EloquentWhereRelationTypeHintingParameterRector;
use RectorLaravel\Rector\MethodCall\EloquentWhereTypeHintClosureParameterRector;
use RectorLaravel\Rector\MethodCall\FactoryApplyingStatesRector;
use RectorLaravel\Rector\MethodCall\JsonCallToExplicitJsonCallRector;
use RectorLaravel\Rector\MethodCall\RedirectBackToBackHelperRector;
use RectorLaravel\Rector\MethodCall\RedirectRouteToToRouteHelperRector;
use RectorLaravel\Rector\MethodCall\RefactorBlueprintGeometryColumnsRector;
use RectorLaravel\Rector\MethodCall\ReplaceServiceContainerCallArgRector;
use RectorLaravel\Rector\MethodCall\ValidationRuleArrayStringValueToArrayRector;
use RectorLaravel\Rector\New_\AddGuardToLoginEventRector;
use RectorLaravel\Rector\PropertyFetch\OptionalToNullsafeOperatorRector;
use RectorLaravel\Rector\StaticCall\EloquentMagicMethodToQueryBuilderRector;
use RectorLaravel\Rector\StaticCall\ReplaceAssertTimesSendWithAssertSentTimesRector;

class Factory
{
    /** @var array<class-string<\Rector\Contract\Rector\RectorInterface>> */
    private array $rules = [];

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

    public function withLaravelRules(): self
    {
        $this->rules = [
            AddGuardToLoginEventRector::class,
            AddParentBootToModelClassMethodRector::class,
            AddParentRegisterToEventServiceProviderRector::class,
            AssertStatusToAssertMethodRector::class,
            EloquentMagicMethodToQueryBuilderRector::class,
            EloquentWhereRelationTypeHintingParameterRector::class,
            EloquentWhereTypeHintClosureParameterRector::class,
            FactoryApplyingStatesRector::class,
            FactoryFuncCallToStaticCallRector::class,
            JsonCallToExplicitJsonCallRector::class,
            OptionalToNullsafeOperatorRector::class,
            PropertyDeferToDeferrableProviderToRector::class,
            RedirectBackToBackHelperRector::class,
            RedirectRouteToToRouteHelperRector::class,
            RefactorBlueprintGeometryColumnsRector::class,
            ReplaceAssertTimesSendWithAssertSentTimesRector::class,
            ReplaceServiceContainerCallArgRector::class,
            SubStrToStartsWithOrEndsWithStaticMethodCallRector::class,
            ValidationRuleArrayStringValueToArrayRector::class,
        ];

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
                ...$this->rules,
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
