<?php

declare(strict_types=1);

namespace Yu\EasyAdminLTEBundle;

use Symfony\Component\Config\Definition\Configurator\DefinitionConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;
use Yu\EasyAdminLTEBundle\DependencyInjection\Compiler\EasyAdminTemplatesPathPass;

/**
 * AdminLTE 4 theme for EasyAdmin.
 *
 * Registers the theme templates (overrides of the "@EasyAdmin" namespace), the "easyadminlte" asset
 * package (public/ of the bundle, installed with "assets:install") and the <twig:AdminLTE:*> Twig components.
 */
class EasyAdminLTEBundle extends AbstractBundle
{
    /** container parameter holding the "enabled" option */
    public const ENABLED_PARAMETER = 'easyadminlte.enabled';

    // configured in config/packages/easyadminlte.yaml (the default alias would be "easy_admin_lte")
    protected string $extensionAlias = 'easyadminlte';

    public function configure(DefinitionConfigurator $definition): void
    {
        $definition->rootNode()
            ->children()
                ->booleanNode('enabled')
                    ->info('Applies the AdminLTE theme to EasyAdmin (templates and assets). The Twig components stay available when disabled.')
                    ->defaultTrue()
                ->end()
            ->end();
    }

    public function build(ContainerBuilder $container): void
    {
        // templates/easyadmin/ is searched for "@EasyAdmin/..." after the application overrides and before
        // EasyAdmin's own templates; the theme templates extend the originals via "@!EasyAdmin/..."
        $container->addCompilerPass(new EasyAdminTemplatesPathPass($this->getPath().'/templates/easyadmin', self::ENABLED_PARAMETER));
    }

    public function prependExtension(ContainerConfigurator $configurator, ContainerBuilder $container): void
    {
        $configurator->extension('framework',
            ['assets' => ['packages' => ['easyadminlte' => ['base_path' => '/bundles/easyadminlte']]]]);

        // Twig components of this bundle: <twig:AdminLTE:SmallBox>, templates in templates/components/
        $configurator->extension('twig_component', ['defaults' => [
            'Yu\\EasyAdminLTEBundle\\Twig\\Component\\' => [
                'template_directory' => '@EasyAdminLTE/components/',
                'name_prefix' => 'AdminLTE',
            ],
        ]]);
    }

    public function loadExtension(array $config, ContainerConfigurator $configurator, ContainerBuilder $container): void
    {
        $container->setParameter(self::ENABLED_PARAMETER, $config['enabled']);

        $exclude = ['./EasyAdminLTEBundle.php', './DependencyInjection/'];
        if (!$config['enabled']) {
            // the listener adds the theme assets to EasyAdmin pages
            $exclude[] = './EventListener/';
        }

        $configurator->services()
            ->defaults()->autowire()->autoconfigure()
            ->load('Yu\\EasyAdminLTEBundle\\', './')
            ->exclude($exclude);
    }
}
