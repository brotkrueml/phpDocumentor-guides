<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Directives;

use phpDocumentor\Guides\RestructuredText\Parser\BlockContext;
use phpDocumentor\Guides\RestructuredText\Parser\Directive;

/**
 * Todo directives are treated as comments, omitting all content or options
 */
class TodoDirective extends ActionDirective
{
    public function getName(): string
    {
        return 'todo';
    }

    public function processAction(BlockContext $blockContext, Directive $directive): void
    {
        // Todo directives are treated as comments
    }
}
