<?php

declare(strict_types=1);

namespace Yu\EasyAdminLTEBundle\Twig\Component;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PostMount;

/**
 * AdminLTE "small box": a colored tile with a big value, a label, a background icon and an optional link.
 *
 *     <twig:AdminLTE:SmallBox value="150" text="New orders" icon="bi bi-cart" variant="success" url="{{ path('...') }}" />
 */
#[AsTwigComponent]
final class SmallBox
{
    use VariantTrait;

    /** the big value at the top (e.g. "150" or "53%"); the "value" block of the template overrides it */
    public string|int|float $value = '';
    /** label below the value; the "content" block of the template overrides it */
    public string $text = '';
    /** CSS classes of an icon font glyph, e.g. "bi bi-cart" or "fa fa-cart-shopping" */
    public ?string $icon = null;
    /** one of VariantTrait::VARIANTS, the background color of the box */
    public string $variant = 'primary';
    /** target of the footer link; NULL hides the footer */
    public ?string $url = null;
    /** text of the footer link */
    public string $linkLabel = 'More info';

    /**
     * Validates the props passed from the template.
     *
     * @throws \InvalidArgumentException on an invalid variant
     */
    #[PostMount]
    public function validate(): void
    {
        $this->assertValidVariant($this->variant);
    }
}
