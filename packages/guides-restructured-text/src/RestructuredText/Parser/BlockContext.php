<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Guides\RestructuredText\Parser;

use function array_merge;

/**
 * Our document parser contains
 */
class BlockContext
{
    private LinesIterator $documentIterator;
    
    public function __construct(
        private readonly DocumentParserContext $documentParserContext,
        string $contents,
        bool $preserveSpace = false,
        private readonly int $lineOffset = 0,
    ) {
        $this->documentIterator = new LinesIterator();
        $this->documentIterator->load($contents, $preserveSpace);
    }
    
    public function getDocumentIterator(): LinesIterator
    {
        return $this->documentIterator;
    }

    public function getDocumentParserContext(): DocumentParserContext
    {
        return $this->documentParserContext;
    }

    /** @return array<string, int|string> */
    public function getLoggerInformation(): array
    {
        $info = [
            'currentLine' => $this->documentIterator->current(),
            'currentLineNumber' => $this->lineOffset + $this->documentIterator->key(),
        ];

        return array_merge($this->getDocumentParserContext()->getLoggerInformation(), $info);
    }
}