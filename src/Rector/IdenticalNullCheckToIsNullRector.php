<?php

declare(strict_types=1);

namespace Airlst\RectorConfig\Rector;

use PhpParser\Node;
use PhpParser\Node\Expr\BinaryOp\Identical;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name;
use Rector\Rector\AbstractRector;
use Symplify\RuleDocGenerator\ValueObject\CodeSample\CodeSample;
use Symplify\RuleDocGenerator\ValueObject\RuleDefinition;

final class IdenticalNullCheckToIsNullRector extends AbstractRector
{
    public function getRuleDefinition(): RuleDefinition
    {
        return new RuleDefinition(
            'Convert `=== null` comparisons to `is_null` function calls',
            [
                new CodeSample(
                    <<<'CODE_SAMPLE'
                        $value === null;
                        CODE_SAMPLE
                    ,
                    <<<'CODE_SAMPLE'
                        is_null($value);
                        CODE_SAMPLE
                ),
            ]
        );
    }

    /**
     * @return array<class-string<Node>>
     */
    public function getNodeTypes(): array
    {
        return [Identical::class];
    }

    /** @param Identical $node */
    public function refactor(Node $node): ?Node
    {
        if (! $this->isNullComparison($node)) {
            return null;
        }

        $value = $node->left instanceof ConstFetch && $node->left->name->toLowerString() === 'null'
            ? $node->right
            : $node->left;

        return new FuncCall(new Name('is_null'), [new Node\Arg($value)]);
    }

    private function isNullComparison(Identical $node): bool
    {
        if ($node->left instanceof ConstFetch && $this->isName($node->left, 'null')) {
            return true;
        }

        return $node->right instanceof ConstFetch && $this->isName($node->right, 'null');
    }
}
