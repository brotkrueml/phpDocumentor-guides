<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\NodeRenderers;

use InvalidArgumentException;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RenderContext;

use function is_array;
use function iterator_to_array;
use function sprintf;

/** @implements NodeRenderer<Node> */
final class OutputAwareDelegatingNodeRenderer implements NodeRenderer
{
    /** @var array<string, NodeRenderer<Node>> */
    private array $nodeRenderers;

    /** @param iterable<string, NodeRenderer<Node>> $nodeRenderers */
    public function __construct(iterable $nodeRenderers)
    {
        if (is_array($nodeRenderers) === false) {
            $nodeRenderers = iterator_to_array($nodeRenderers);
        }

        $this->nodeRenderers = $nodeRenderers;
    }

    public function supports(Node $node): bool
    {
        return true;
    }

    public function render(Node $node, RenderContext $renderContext): string
    {
        if (isset($this->nodeRenderers[$renderContext->getOutputFormat()]) === false) {
            throw new InvalidArgumentException(
                sprintf('No node renderer found for output format "%s"', $renderContext->getOutputFormat()),
            );
        }

        return $this->nodeRenderers[$renderContext->getOutputFormat()]->render($node, $renderContext);
    }
}
