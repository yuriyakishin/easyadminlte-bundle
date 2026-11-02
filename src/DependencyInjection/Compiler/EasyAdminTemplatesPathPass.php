<?php

declare(strict_types=1);

namespace Yu\EasyAdminLTEBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Adds the bundle's EasyAdmin template overrides to the "@EasyAdmin" Twig namespace.
 *
 * TwigBundle registers the "@EasyAdmin" paths as: the application overrides
 * (templates/bundles/EasyAdminBundle/, if that directory exists) followed by EasyAdmin's own templates.
 * This pass inserts the theme templates right before EasyAdmin's own ones, so the lookup order is
 * application overrides > this theme > EasyAdmin. (Paths added with the "twig.paths" option would be
 * registered before the application overrides, so the theme would win over the application.)
 */
final class EasyAdminTemplatesPathPass implements CompilerPassInterface
{
    private const NAMESPACE = 'EasyAdmin';

    /**
     * @param string $templatesPath    absolute path of the theme templates (overrides of "@EasyAdmin/..." templates)
     * @param string $enabledParameter container parameter holding the "easyadminlte.enabled" option
     */
    public function __construct(
        private readonly string $templatesPath,
        private readonly string $enabledParameter,
    ) {
    }

    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('twig.loader.native_filesystem')) {
            return;
        }

        // the theme is disabled with "easyadminlte: { enabled: false }"
        if ($container->hasParameter($this->enabledParameter) && !$container->getParameter($this->enabledParameter)) {
            return;
        }

        $loader = $container->getDefinition('twig.loader.native_filesystem');
        $calls = $loader->getMethodCalls();

        // EasyAdmin's own templates directory is always the last path of its namespace
        $easyAdminPathIndex = null;
        foreach ($calls as $index => [$method, $arguments]) {
            if ('addPath' === $method && self::NAMESPACE === ($arguments[1] ?? null)) {
                $easyAdminPathIndex = $index;
            }
        }

        if (null === $easyAdminPathIndex) {
            return;
        }

        $path = $container->getParameterBag()->escapeValue($this->templatesPath);
        array_splice($calls, $easyAdminPathIndex, 0, [['addPath', [$path, self::NAMESPACE]]]);
        $loader->setMethodCalls($calls);
    }
}
