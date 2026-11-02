<?php

declare(strict_types=1);

namespace Yu\EasyAdminLTEBundle\EventListener;

use EasyCorp\Bundle\EasyAdminBundle\Config\Asset;
use EasyCorp\Bundle\EasyAdminBundle\Config\Option\EA;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\Event\RequestEvent;

/**
 * Adds the AdminLTE theme assets (CSS and JS) to every EasyAdmin page.
 *
 * The assets are added to the AdminContext of the request, so EasyAdmin renders them in its layout
 * like the assets configured with configureAssets() in a dashboard or CRUD controller.
 *
 * The request listener runs after EasyAdmin's AdminRouterSubscriber (priority 1), which creates the
 * AdminContext. When creating the AdminContext fails (e.g. entity not found) the request listener never
 * runs; EasyAdmin then renders its error page in the dashboard layout from its ExceptionListener
 * (priority -64), so the exception listener adds the assets before that.
 */
#[AsEventListener(event: RequestEvent::class, priority: -1)]
#[AsEventListener(event: ExceptionEvent::class, method: 'onKernelException', priority: 0)]
class AdminLTERouterSubscriber
{
    private const PACKAGE = 'easyadminlte';
    private const CSS_FILES = ['css/easyadminlte.css'];
    private const JS_FILES = ['js/adminlte.min.js', 'js/easyadminlte.js'];

    public function __invoke(RequestEvent $event): void
    {
        $this->addAssets($event);
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $this->addAssets($event);
    }

    /**
     * Does nothing outside EasyAdmin (no AdminContext in the request attributes).
     */
    private function addAssets(KernelEvent $event): void
    {
        $context = $event->getRequest()->attributes->get(EA::CONTEXT_REQUEST_ATTRIBUTE);

        if (!$context instanceof AdminContext) {
            return;
        }

        // EasyAdmin's AssetsDto throws when the same file is added twice (e.g. an exception thrown by
        // the controller, after the request listener already added the assets)
        $assets = $context->getAssets();
        foreach (self::CSS_FILES as $path) {
            if (!\array_key_exists($path, $assets->getCssAssets())) {
                $assets->addCssAsset(Asset::new($path)->package(self::PACKAGE)->getAsDto());
            }
        }
        foreach (self::JS_FILES as $path) {
            if (!\array_key_exists($path, $assets->getJsAssets())) {
                $assets->addJsAsset(Asset::new($path)->package(self::PACKAGE)->defer()->getAsDto());
            }
        }
    }
}
