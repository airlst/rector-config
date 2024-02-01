<?php

declare(strict_types=1);

namespace Airlst\RectorConfig;

use Airlst\RectorConfig\Rector\UnderscoreToCamelCaseVariableNameRector;
use Rector\Caching\ValueObject\Storage\FileCacheStorage;
use Rector\CodeQuality\Rector\Class_\InlineConstructorDefaultToPropertyRector;
use Rector\Config\RectorConfig;
use Rector\Php81\Rector\ClassConst\FinalizePublicClassConstantRector;
use Rector\Privatization\Rector\MethodCall\PrivatizeLocalGetterToPropertyRector;
use Rector\Set\ValueObject\LevelSetList;
use Rector\Set\ValueObject\SetList;
use Rector\TypeDeclaration\Rector\ArrowFunction\AddArrowFunctionReturnTypeRector;
use Rector\TypeDeclaration\Rector\Class_\PropertyTypeFromStrictSetterGetterRector;
use Rector\TypeDeclaration\Rector\Class_\ReturnTypeFromStrictTernaryRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddMethodCallBasedStrictParamTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddParamTypeBasedOnPHPUnitDataProviderRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddParamTypeFromPropertyTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddReturnTypeDeclarationBasedOnParentClassMethodRector;
use Rector\TypeDeclaration\Rector\ClassMethod\AddVoidReturnTypeWhereNoReturnRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ParamTypeByMethodCallTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ParamTypeByParentCallTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnNeverTypeRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromReturnDirectArrayRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromReturnNewRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictBoolReturnExprRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictConstantReturnRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictNativeCallRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictNewArrayRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictScalarReturnExprRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictTypedCallRector;
use Rector\TypeDeclaration\Rector\ClassMethod\ReturnTypeFromStrictTypedPropertyRector;
use Rector\TypeDeclaration\Rector\Empty_\EmptyOnNullableObjectToInstanceOfRector;
use Rector\TypeDeclaration\Rector\FunctionLike\AddParamTypeSplFixedArrayRector;
use Rector\TypeDeclaration\Rector\FunctionLike\AddReturnTypeDeclarationFromYieldsRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromAssignsRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictConstructorRector;
use Rector\TypeDeclaration\Rector\Property\TypedPropertyFromStrictSetUpRector;

use function is_null;

class Factory
{
    private ?string $phpLevelSet = LevelSetList::UP_TO_PHP_83;
    private ?string $cacheDirectory = null;

    public function __construct(private readonly array $directories) {}

    public function useFileCache(string $cacheDirectory): self
    {
        $this->cacheDirectory = $cacheDirectory;

        return $this;
    }

    public function php82(): self
    {
        $this->phpLevelSet = LevelSetList::UP_TO_PHP_82;

        return $this;
    }

    public function create(RectorConfig $rectorConfig): void
    {
        if (! is_null($this->cacheDirectory)) {
            $rectorConfig->cacheClass(FileCacheStorage::class);
            $rectorConfig->cacheDirectory($this->cacheDirectory);
        }

        $rectorConfig->paths($this->directories);

        $rectorConfig->importNames(importDocBlockNames: false);

        $rectorConfig->sets([
            $this->phpLevelSet,
            SetList::CODE_QUALITY,
            SetList::EARLY_RETURN,
            SetList::DEAD_CODE,
        ]);

        $rectorConfig->rules([
            InlineConstructorDefaultToPropertyRector::class,
            UnderscoreToCamelCaseVariableNameRector::class,

            // From PRIVATIZATION set
            PrivatizeLocalGetterToPropertyRector::class,
            FinalizePublicClassConstantRector::class,

            // From TYPE_DECLARATION set
            AddArrowFunctionReturnTypeRector::class,
            ParamTypeByMethodCallTypeRector::class,
            TypedPropertyFromAssignsRector::class,
            AddReturnTypeDeclarationBasedOnParentClassMethodRector::class,
            ReturnTypeFromStrictTypedPropertyRector::class,
            TypedPropertyFromStrictConstructorRector::class,
            AddVoidReturnTypeWhereNoReturnRector::class,
            ReturnTypeFromReturnNewRector::class,
            AddMethodCallBasedStrictParamTypeRector::class,
            ReturnTypeFromStrictBoolReturnExprRector::class,
            ReturnTypeFromStrictNativeCallRector::class,
            ReturnTypeFromStrictNewArrayRector::class,
            ReturnTypeFromStrictScalarReturnExprRector::class,
            TypedPropertyFromStrictSetUpRector::class,
            ParamTypeByParentCallTypeRector::class,
            AddParamTypeSplFixedArrayRector::class,
            AddParamTypeBasedOnPHPUnitDataProviderRector::class,
            AddParamTypeFromPropertyTypeRector::class,
            AddReturnTypeDeclarationFromYieldsRector::class,
            ReturnTypeFromReturnDirectArrayRector::class,
            ReturnTypeFromStrictConstantReturnRector::class,
            ReturnTypeFromStrictTypedCallRector::class,
            ReturnNeverTypeRector::class,
            EmptyOnNullableObjectToInstanceOfRector::class,
            PropertyTypeFromStrictSetterGetterRector::class,
            ReturnTypeFromStrictTernaryRector::class,
        ]);
    }
}
