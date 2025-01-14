<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Productions\InlineRules;

use phpDocumentor\Guides\Nodes\Inline\InlineNode;
use phpDocumentor\Guides\RestructuredText\Parser\BlockContext;
use phpDocumentor\Guides\RestructuredText\Parser\InlineLexer;

class InternalReferenceRule extends ReferenceRule
{
    public function applies(InlineLexer $lexer): bool
    {
        return $lexer->token?->type === InlineLexer::UNDERSCORE;
    }

    public function apply(BlockContext $blockContext, InlineLexer $lexer): InlineNode|null
    {
        $text = '';
        $initialPosition = $lexer->token?->position;
        $lexer->moveNext();
        if ($lexer->token?->type !== InlineLexer::BACKTICK) {
            $this->rollback($lexer, $initialPosition ?? 0);

            return null;
        }

        $lexer->moveNext();
        while ($lexer->token !== null) {
            switch ($lexer->token->type) {
                case InlineLexer::BACKTICK:
                    $lexer->moveNext();

                    return $this->createReference($blockContext, $text);

                default:
                    $text .= $lexer->token->value;
            }

            $lexer->moveNext();
        }

        $lexer->resetPosition($initialPosition ?? 0);
        $lexer->moveNext();
        $lexer->moveNext();

        return null;
    }

    public function getPriority(): int
    {
        return 1000;
    }
}
