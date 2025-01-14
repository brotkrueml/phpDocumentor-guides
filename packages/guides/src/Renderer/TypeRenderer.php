<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderer;

use phpDocumentor\Guides\Handlers\RenderCommand;

interface TypeRenderer
{
    public function render(RenderCommand $renderCommand): void;
}
