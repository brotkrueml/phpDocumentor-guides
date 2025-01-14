<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Productions\InlineRules;

use phpDocumentor\Guides\Nodes\Inline\PlainTextInlineNode;
use phpDocumentor\Guides\RestructuredText\Parser\BlockContext;
use phpDocumentor\Guides\RestructuredText\Parser\InlineLexer;

use function preg_match;
use function substr;

/**
 * Rule to escape characters with a backslash
 */
class EscapeRule extends ReferenceRule
{
    public function applies(InlineLexer $lexer): bool
    {
        return $lexer->token?->type === InlineLexer::ESCAPED_SIGN;
    }

    public function apply(BlockContext $blockContext, InlineLexer $lexer): PlainTextInlineNode|null
    {
        $char = $lexer->token?->value ?? '';
        $char = substr($char, 1);
        $lexer->moveNext();

        if (preg_match('/^\s$/', $char)) {
            return null;
        }

        return new PlainTextInlineNode($char);
    }

    public function getPriority(): int
    {
        return 1000;
    }
}
