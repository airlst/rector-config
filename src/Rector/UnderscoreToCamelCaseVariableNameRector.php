<?php

declare(strict_types=1);

/**
 * From CodeIgniter 4:
 * https://github.com/codeigniter4/CodeIgniter4/blob/develop/utils/Rector/UnderscoreToCamelCaseVariableNameRector.php.
 */

namespace Airlst\RectorConfig\Rector;

use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Expr\Closure;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\FunctionLike;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Function_;
use PhpParser\Node\Stmt\Namespace_;
use Rector\BetterPhpDocParser\PhpDocInfo\PhpDocInfoFactory;
use Rector\Php\ReservedKeywordAnalyzer;
use Rector\PhpParser\Node\CustomNode\FileWithoutNamespace;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

use function is_null;

/**
 * Adapted from https://github.com/rectorphp/rector/blob/4578e6d8490c1acfbf59bb17c4537a672fa77193/rules/naming/src/Rector/Variable/UnderscoreToCamelCaseVariableNameRector.php
 * with skip _ in first character\.
 */
final class UnderscoreToCamelCaseVariableNameRector extends AbstractRector
{
    /**
     * @see https://regex101.com/r/OtFn8I/1
     */
    private const PARAM_NAME_REGEX = '#(?<paramPrefix>@param\s.*\s+\$)(?<paramName>%s)#ms';

    private bool $hasChanged = false;

    public function __construct(private readonly ReservedKeywordAnalyzer $reservedKeywordAnalyzer, private readonly PhpDocInfoFactory $phpDocInfoFactory) {}

    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition('Change under_score names to camelCase', [
            new CodeSample(
                <<<'CODE_SAMPLE'
                    final class SomeClass
                    {
                        public function run($a_b)
                        {
                            $some_value = $a_b;
                        }
                    }
                    CODE_SAMPLE
                ,
                <<<'CODE_SAMPLE'
                    final class SomeClass
                    {
                        public function run($aB)
                        {
                            $someValue = $aB;
                        }
                    }
                    CODE_SAMPLE
            ),
        ]);
    }

    /**
     * @return string[]
     */
    public function getNodeTypes(): array
    {
        return [FileWithoutNamespace::class, Namespace_::class];
    }

    /**
     * @param ClassMethod|Closure|FileWithoutNamespace|Function_|Namespace_ $node
     */
    public function refactor(Node $node): ?Node
    {
        if (is_null($node->stmts)) {
            return null;
        }

        $this->hasChanged = false;

        $this->traverseNodesWithCallable(
            $node->stmts,
            function (Node $subNode): void { // @phpstan-ignore-line
                if ($subNode instanceof Variable || $subNode instanceof ClassMethod || $subNode instanceof Function_ || $subNode instanceof Closure) {
                    $this->processRenameVariable($subNode);
                }
            }
        );

        if ($this->hasChanged) {
            return $node;
        }

        return null;
    }

    /**
     * @param FunctionLike|Variable $node
     */
    private function processRenameVariable(Node $node): ?Variable
    {
        if ($node instanceof FunctionLike) {
            if ($node instanceof Closure) {
                foreach ($node->uses as $closureUse) {
                    $this->processRenameVariable($closureUse->var);
                }
            }

            foreach ($node->params as $key => $param) { // @phpstan-ignore-line
                $originalVariableName = $param->var->name;
                $variable = $this->processRenameVariable($param->var);
                if ($variable instanceof Variable) {
                    $node->params[$key]->var = $variable; // @phpstan-ignore-line
                    $this->updateDocblock($originalVariableName, $variable->name, $node); // @phpstan-ignore-line
                }
            }

            return null;
        }

        $nodeName = $this->getName($node);
        if (is_null($nodeName)) {
            return null;
        }

        if ($this->reservedKeywordAnalyzer->isNativeVariable($nodeName)) {
            return null;
        }

        $underscorePosition = strpos($nodeName, '_');
        // underscore not found, or in the first char, skip
        if ((int) $underscorePosition === 0) {
            return null;
        }

        $replaceUnderscoreToSpace = str_replace('_', ' ', $nodeName);
        $uppercaseFirstChar = ucwords($replaceUnderscoreToSpace);
        $camelCaseName = lcfirst(str_replace(' ', '', $uppercaseFirstChar));

        if ($camelCaseName === 'this') {
            return null;
        }

        $node->name = $camelCaseName;
        $this->hasChanged = true;

        return $node;
    }

    private function updateDocblock(string $variableName, string $camelCaseName, ?FunctionLike $functionLike): void
    {
        if (! $functionLike instanceof FunctionLike) {
            return;
        }

        $docComment = $functionLike->getDocComment();
        if (is_null($docComment)) {
            return;
        }

        $docCommentText = $docComment->getText();
        if (is_null($docCommentText)) { // @phpstan-ignore-line
            return;
        }

        if (preg_match(sprintf(self::PARAM_NAME_REGEX, $variableName), $docCommentText) === 0) {
            return;
        }

        $phpDocInfo = $this->phpDocInfoFactory->createFromNodeOrEmpty($functionLike);
        $paramTagValueNodes = $phpDocInfo->getParamTagValueNodes();

        foreach ($paramTagValueNodes as $paramTagValueNode) {
            if ($paramTagValueNode->parameterName === '$' . $variableName) {
                $paramTagValueNode->parameterName = '$' . $camelCaseName;

                break;
            }
        }

        $functionLike->setDocComment(new Doc($phpDocInfo->getPhpDocNode()->__toString()));
    }
}
