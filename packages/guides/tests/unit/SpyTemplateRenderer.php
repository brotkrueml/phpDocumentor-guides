<?php

declare(strict_types=1);

namespace phpDocumentor\Guides;

use phpDocumentor\Guides\Nodes\Node;

class SpyTemplateRenderer implements TemplateRenderer
{
    /** @var mixed[] */
    private array $context;
    private string $template;

    /** @param mixed[] $params */
    public function renderTemplate(RenderContext $context, string $template, array $params = []): string
    {
        $this->context = $params;
        $this->template = $template;

        return 'spy';
    }

    /** @return mixed[] */
    public function getContext(): array
    {
        return $this->context;
    }

    public function getTemplate(): string
    {
        return $this->template;
    }

    public function renderNode(Node $node, RenderContext $context): string
    {
        return '';
    }

    public function isTemplateFound(RenderContext $context, string $template): bool
    {
        return true;
    }
}
