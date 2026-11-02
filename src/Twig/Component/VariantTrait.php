<?php

declare(strict_types=1);

namespace Yu\EasyAdminLTEBundle\Twig\Component;

/**
 * Bootstrap theme color of a component ("text-bg-*", "card-*" CSS classes).
 */
trait VariantTrait
{
    /** @var list<string> */
    public const VARIANTS = ['primary', 'secondary', 'success', 'danger', 'warning', 'info', 'light', 'dark'];

    /**
     * @param string|null $variant NULL is allowed for components with a neutral look (e.g. Card)
     *
     * @throws \InvalidArgumentException when the variant is not one of self::VARIANTS
     */
    private function assertValidVariant(?string $variant): void
    {
        if (null !== $variant && !\in_array($variant, self::VARIANTS, true)) {
            throw new \InvalidArgumentException(\sprintf('The "%s" variant is not valid for the "%s" component; use one of: "%s".', $variant, static::class, implode('", "', self::VARIANTS)));
        }
    }
}
