<?php

declare(strict_types=1);

namespace Yu\EasyAdminLTEBundle\Twig\Component;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PostMount;

/**
 * AdminLTE "info box": an icon square next to a label and a number, optionally with a progress bar.
 *
 *     <twig:AdminLTE:InfoBox text="CPU traffic" number="10" unit="%" icon="bi bi-gear-fill" variant="info" />
 *     <twig:AdminLTE:InfoBox text="Likes" number="41,410" icon="bi bi-hand-thumbs-up" variant="success" filled progress="70" description="70% increase in 30 days" />
 */
#[AsTwigComponent]
final class InfoBox
{
    use VariantTrait;

    /** label above the number; the "content" block of the template overrides it */
    public string $text = '';
    /** the main value, rendered in bold (e.g. "41,410"); format numbers in the template */
    public string|int|float $number = '';
    /** rendered smaller, right after the number (e.g. "%") */
    public ?string $unit = null;
    /** CSS classes of an icon font glyph, e.g. "bi bi-gear-fill" */
    public ?string $icon = null;
    /** one of VariantTrait::VARIANTS, colors the icon square (or the whole box if "filled") */
    public string $variant = 'primary';
    /** TRUE colors the whole box instead of only the icon square */
    public bool $filled = false;
    /** 0-100; NULL hides the progress bar */
    public ?int $progress = null;
    /** small text below the progress bar */
    public ?string $description = null;

    /**
     * Validates the props passed from the template.
     *
     * @throws \InvalidArgumentException on an invalid variant or a progress outside 0-100
     */
    #[PostMount]
    public function validate(): void
    {
        $this->assertValidVariant($this->variant);

        if (null !== $this->progress && ($this->progress < 0 || $this->progress > 100)) {
            throw new \InvalidArgumentException(\sprintf('The "progress" of the "%s" component must be between 0 and 100, %d given.', self::class, $this->progress));
        }
    }
}
