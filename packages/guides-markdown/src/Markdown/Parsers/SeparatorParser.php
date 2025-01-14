<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Markdown\Parsers;

use League\CommonMark\Extension\CommonMark\Node\Block\ThematicBreak;
use League\CommonMark\Node\Node as CommonMarkNode;
use League\CommonMark\Node\NodeWalker;
use League\CommonMark\Node\NodeWalkerEvent;
use phpDocumentor\Guides\MarkupLanguageParser;
use phpDocumentor\Guides\Nodes\SeparatorNode;

/** @extends AbstractBlockParser<SeparatorNode> */
final class SeparatorParser extends AbstractBlockParser
{
    public function parse(MarkupLanguageParser $parser, NodeWalker $walker, CommonMarkNode $current): SeparatorNode
    {
        $walker->next();

        return new SeparatorNode(1);
    }

    public function supports(NodeWalkerEvent $event): bool
    {
        return $event->getNode() instanceof ThematicBreak;
    }
}
