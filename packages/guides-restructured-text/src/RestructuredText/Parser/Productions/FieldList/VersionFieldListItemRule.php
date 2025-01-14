<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Productions\FieldList;

use phpDocumentor\Guides\Nodes\FieldLists\FieldListItemNode;
use phpDocumentor\Guides\Nodes\Metadata\MetadataNode;
use phpDocumentor\Guides\RestructuredText\Parser\BlockContext;
use Psr\Log\LoggerInterface;

use function sprintf;
use function strtolower;

class VersionFieldListItemRule implements FieldListItemRule
{
    public function __construct(private readonly LoggerInterface $logger)
    {
    }

    public function applies(FieldListItemNode $fieldListItemNode): bool
    {
        return strtolower($fieldListItemNode->getTerm()) === 'version';
    }

    public function apply(FieldListItemNode $fieldListItemNode, BlockContext $blockContext): MetadataNode|null
    {
        $currentVersion = $blockContext->getDocumentParserContext()->getProjectNode()->getVersion();
        if (
            $currentVersion !== null
            && $currentVersion !== $fieldListItemNode->getPlaintextContent()
        ) {
            $this->logger->warning(sprintf(
                'Project version was set more then once: %s and %s',
                $currentVersion,
                $fieldListItemNode->getPlaintextContent(),
            ), $blockContext->getLoggerInformation());
        }

        $blockContext->getDocumentParserContext()->getProjectNode()->setVersion($fieldListItemNode->getPlaintextContent());

        return null;
    }
}
