<?php

declare(strict_types=1);

use League\Tactician\CommandBus;
use phpDocumentor\Guides\Compiler\Compiler;
use phpDocumentor\Guides\Compiler\CompilerPass;
use phpDocumentor\Guides\Compiler\DocumentNodeTraverser;
use phpDocumentor\Guides\Compiler\NodeTransformer;
use phpDocumentor\Guides\Compiler\NodeTransformers\CustomNodeTransformerFactory;
use phpDocumentor\Guides\Compiler\NodeTransformers\NodeTransformerFactory;
use phpDocumentor\Guides\Interlink\InventoryLoader;
use phpDocumentor\Guides\Interlink\InventoryRepository;
use phpDocumentor\Guides\Interlink\JsonLoader;
use phpDocumentor\Guides\NodeRenderers\Html\BreadCrumbNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\Html\DocumentNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\Html\MenuEntryRenderer;
use phpDocumentor\Guides\NodeRenderers\Html\MenuNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\Html\TableNodeRenderer;
use phpDocumentor\Guides\NodeRenderers\OutputAwareDelegatingNodeRenderer;
use phpDocumentor\Guides\Parser;
use phpDocumentor\Guides\ReferenceResolvers\AnchorReducer;
use phpDocumentor\Guides\ReferenceResolvers\AnchorReferenceResolver;
use phpDocumentor\Guides\ReferenceResolvers\DelegatingReferenceResolver;
use phpDocumentor\Guides\ReferenceResolvers\DocReferenceResolver;
use phpDocumentor\Guides\ReferenceResolvers\DocumentNameResolver;
use phpDocumentor\Guides\ReferenceResolvers\DocumentNameResolverInterface;
use phpDocumentor\Guides\ReferenceResolvers\EmailReferenceResolver;
use phpDocumentor\Guides\ReferenceResolvers\ExternalReferenceResolver;
use phpDocumentor\Guides\ReferenceResolvers\InterlinkReferenceResolver;
use phpDocumentor\Guides\ReferenceResolvers\InternalReferenceResolver;
use phpDocumentor\Guides\ReferenceResolvers\ReferenceResolver;
use phpDocumentor\Guides\ReferenceResolvers\ReferenceResolverPreRender;
use phpDocumentor\Guides\ReferenceResolvers\SluggerAnchorReducer;
use phpDocumentor\Guides\Renderer\HtmlRenderer;
use phpDocumentor\Guides\Renderer\InMemoryRendererFactory;
use phpDocumentor\Guides\Renderer\InterlinkObjectsRenderer;
use phpDocumentor\Guides\Renderer\LatexRenderer;
use phpDocumentor\Guides\Renderer\TypeRendererFactory;
use phpDocumentor\Guides\Renderer\UrlGenerator\AbsoluteUrlGenerator;
use phpDocumentor\Guides\Renderer\UrlGenerator\AbstractUrlGenerator;
use phpDocumentor\Guides\Renderer\UrlGenerator\ConfigurableUrlGenerator;
use phpDocumentor\Guides\Renderer\UrlGenerator\RelativeUrlGenerator;
use phpDocumentor\Guides\Renderer\UrlGenerator\UrlGeneratorInterface;
use phpDocumentor\Guides\Settings\SettingsManager;
use phpDocumentor\Guides\TemplateRenderer;
use phpDocumentor\Guides\Twig\AssetsExtension;
use phpDocumentor\Guides\Twig\EnvironmentBuilder;
use phpDocumentor\Guides\Twig\Theme\ThemeManager;
use phpDocumentor\Guides\Twig\TwigTemplateRenderer;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Twig\Loader\FilesystemLoader;

use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use function Symfony\Component\DependencyInjection\Loader\Configurator\tagged_iterator;

return static function (ContainerConfigurator $container): void {
    $container->parameters()
        ->set('phpdoc.guides.base_template_paths', [__DIR__ . '/../../../guides/resources/template/html']);

    $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()

        ->instanceof(CompilerPass::class)
        ->tag('phpdoc.guides.compiler.passes')

        ->instanceof(NodeTransformer::class)
        ->tag('phpdoc.guides.compiler.nodeTransformers')

        ->instanceof(ReferenceResolver::class)
        ->tag('phpdoc.guides.reference_resolver')

        ->load(
            'phpDocumentor\\Guides\\Compiler\\NodeTransformers\\',
            '%vendor_dir%/phpdocumentor/guides/src/Compiler/NodeTransformers/*Transformer.php',
        )

        ->load(
            'phpDocumentor\\Guides\\Compiler\\Passes\\',
            '%vendor_dir%/phpdocumentor/guides/src/Compiler/Passes/*Pass.php',
        )

        ->set(AbsoluteUrlGenerator::class)
        ->set(RelativeUrlGenerator::class)
        ->set(UrlGeneratorInterface::class, ConfigurableUrlGenerator::class)
        ->set(DocumentNameResolverInterface::class, DocumentNameResolver::class)

        ->set(Parser::class)
        ->arg('$parserStrategies', tagged_iterator('phpdoc.guides.parser.markupLanguageParser'))

        ->set(Compiler::class)
        ->arg('$passes', tagged_iterator('phpdoc.guides.compiler.passes'))

        ->set(NodeTransformerFactory::class, CustomNodeTransformerFactory::class)
        ->arg('$transformers', tagged_iterator('phpdoc.guides.compiler.nodeTransformers'))

        ->set(SettingsManager::class)

        ->set(DocumentNodeTraverser::class)

        ->set(InventoryRepository::class)

        ->set(InventoryLoader::class)

        ->set(JsonLoader::class)


        ->set(HttpClientInterface::class)
        ->factory([HttpClient::class, 'create'])

        ->set(AbstractUrlGenerator::class)

        ->set(ExternalReferenceResolver::class)

        ->set(EmailReferenceResolver::class)

        ->set(AnchorReferenceResolver::class)

        ->set(InternalReferenceResolver::class)

        ->set(DocReferenceResolver::class)

        ->set(InterlinkReferenceResolver::class)

        ->set(DelegatingReferenceResolver::class)
        ->arg('$resolvers', tagged_iterator('phpdoc.guides.reference_resolver', defaultPriorityMethod: 'getPriority'))

        ->set(HtmlRenderer::class)
        ->tag(
            'phpdoc.renderer.typerenderer',
            [
                'noderender_tag' => 'phpdoc.guides.noderenderer.html',
                'format' => 'html',
            ],
        )
        ->args(
            ['$commandBus' => service(CommandBus::class)],
        )
        ->set(LatexRenderer::class)
        ->tag(
            'phpdoc.renderer.typerenderer',
            [
                'noderender_tag' => 'phpdoc.guides.noderenderer.tex',
                'format' => 'tex',
            ],
        )

        ->set(InterlinkObjectsRenderer::class)
        ->tag(
            'phpdoc.renderer.typerenderer',
            ['format' => 'interlink'],
        )

        ->set(DocumentNodeRenderer::class)
        ->tag('phpdoc.guides.noderenderer.html')
        ->set(TableNodeRenderer::class)
        ->tag('phpdoc.guides.noderenderer.html')
        ->set(MenuNodeRenderer::class)
        ->tag('phpdoc.guides.noderenderer.html')
        ->set(MenuEntryRenderer::class)
        ->tag('phpdoc.guides.noderenderer.html')
        ->set(BreadCrumbNodeRenderer::class)
        ->tag('phpdoc.guides.noderenderer.html')

        ->set(ReferenceResolverPreRender::class)
        ->tag('phpdoc.guides.prerenderer')

        ->set(InMemoryRendererFactory::class)
        ->arg('$renderSets', tagged_iterator('phpdoc.renderer.typerenderer', 'format'))
        ->alias(TypeRendererFactory::class, InMemoryRendererFactory::class)

        ->set(SluggerAnchorReducer::class)
        ->alias(AnchorReducer::class, SluggerAnchorReducer::class)

        ->set('phpdoc.guides.output_node_renderer', OutputAwareDelegatingNodeRenderer::class)
        ->arg('$nodeRenderers', tagged_iterator('phpdoc.guides.output_node_renderer', 'format'))

        ->set(AssetsExtension::class)
        ->arg('$nodeRenderer', service('phpdoc.guides.output_node_renderer'))
        ->tag('twig.extension')
        ->autowire()

        ->set(ThemeManager::class)
        ->arg('$filesystemLoader', service(FilesystemLoader::class))
        ->arg(
            '$defaultPaths',
            param('phpdoc.guides.base_template_paths'),
        )

        ->set(FilesystemLoader::class)
        ->arg(
            '$paths',
            param('phpdoc.guides.base_template_paths'),
        )

        ->set(EnvironmentBuilder::class)
        ->arg('$extensions', tagged_iterator('twig.extension'))
        ->arg('$themeManager', service(ThemeManager::class))

        ->set(TemplateRenderer::class, TwigTemplateRenderer::class)
        ->arg('$environmentBuilder', new Reference(EnvironmentBuilder::class));
};
