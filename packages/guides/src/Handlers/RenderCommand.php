<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Handlers;

use League\Flysystem\FilesystemInterface;
use phpDocumentor\Guides\Nodes\DocumentNode;
use phpDocumentor\Guides\Nodes\ProjectNode;
use phpDocumentor\Guides\Renderer\DocumentListIterator;
use phpDocumentor\Guides\Renderer\DocumentTreeIterator;

final class RenderCommand
{
    private DocumentListIterator $documentIterator;

    /** @param DocumentNode[] $documentArray */
    public function __construct(
        private readonly string $outputFormat,
        private readonly array $documentArray,
        private readonly FilesystemInterface $origin,
        private readonly FilesystemInterface $destination,
        private readonly ProjectNode $projectNode,
        private readonly string $destinationPath = '/',
    ) {
        $this->documentIterator = new DocumentListIterator(
            new DocumentTreeIterator(
                [$this->projectNode->getRootDocumentEntry()],
                $this->documentArray,
            ),
            $this->documentArray,
        );
    }

    public function getOutputFormat(): string
    {
        return $this->outputFormat;
    }

    /** @return DocumentNode[] $documentArray */
    public function getDocumentArray(): array
    {
        return $this->documentArray;
    }

    public function getDocumentIterator(): DocumentListIterator
    {
        return $this->documentIterator;
    }

    public function getOrigin(): FilesystemInterface
    {
        return $this->origin;
    }

    public function getDestination(): FilesystemInterface
    {
        return $this->destination;
    }

    public function getDestinationPath(): string
    {
        return $this->destinationPath;
    }

    public function getProjectNode(): ProjectNode
    {
        return $this->projectNode;
    }
}
