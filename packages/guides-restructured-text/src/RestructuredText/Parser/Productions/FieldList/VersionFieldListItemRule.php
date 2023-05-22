<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Parser\Productions\FieldList;

use phpDocumentor\Guides\Meta\ProjectMeta;
use phpDocumentor\Guides\Nodes\FieldLists\FieldListItemNode;
use phpDocumentor\Guides\Nodes\Metadata\MetadataNode;

use function strtolower;

class VersionFieldListItemRule implements FieldListItemRule
{
    public function __construct(private readonly ProjectMeta $projectMeta)
    {
    }

    public function applies(FieldListItemNode $fieldListItemNode): bool
    {
        return strtolower($fieldListItemNode->getTerm()) === 'version';
    }

    public function apply(FieldListItemNode $fieldListItemNode): MetadataNode|null
    {
        $this->projectMeta->setVersion($fieldListItemNode->getPlaintextContent());

        return null;
    }
}