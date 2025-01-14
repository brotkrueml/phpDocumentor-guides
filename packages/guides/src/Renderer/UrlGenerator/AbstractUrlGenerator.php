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

namespace phpDocumentor\Guides\Renderer\UrlGenerator;

use Exception;
use League\Uri\UriInfo;
use phpDocumentor\Guides\ReferenceResolvers\DocumentNameResolverInterface;
use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Guides\UriFactory;

use function sprintf;

abstract class AbstractUrlGenerator implements UrlGeneratorInterface
{
    public function __construct(private readonly DocumentNameResolverInterface $documentNameResolver)
    {
    }

    public function createFileUrl(RenderContext $context, string $filename, string|null $anchor = null): string
    {
        return $filename . '.' . $context->getOutputFormat() .
            ($anchor !== null ? '#' . $anchor : '');
    }

    abstract public function generateInternalPathFromRelativeUrl(
        RenderContext $renderContext,
        string $canonicalUrl,
    ): string;

    /**
     * Generate a canonical output URL with the configured file extension and anchor
     */
    public function generateCanonicalOutputUrl(RenderContext $context, string $linkedDocument, string|null $anchor = null): string
    {
        if ($context->getProjectNode()->findDocumentEntry($linkedDocument) !== null) {
            // todo: this is a hack, existing documents are expected to be handled like absolute links in some places
            $linkedDocument = '/' . $linkedDocument;
        }

        $canonicalUrl = $this->documentNameResolver->canonicalUrl(
            $context->getDirName(),
            $linkedDocument,
        );

        return $this->generateInternalUrl(
            $context,
            $this->createFileUrl($context, $canonicalUrl, $anchor),
        );
    }

    public function generateInternalUrl(
        RenderContext $renderContext,
        string $canonicalUrl,
    ): string {
        if (!$this->isRelativeUrl($canonicalUrl)) {
            throw new Exception(sprintf('%s::%s may only be applied to relative URLs, %s cannot be handled', self::class, __METHOD__, $canonicalUrl));
        }

        return $this->generateInternalPathFromRelativeUrl($renderContext, $canonicalUrl);
    }

    private function isRelativeUrl(string $url): bool
    {
        $uri = UriFactory::createUri($url);

        return UriInfo::isRelativePath($uri);
    }

    public function getCurrentFileUrl(RenderContext $renderContext): string
    {
        return $this->createFileUrl($renderContext, $renderContext->getCurrentFileName());
    }
}
