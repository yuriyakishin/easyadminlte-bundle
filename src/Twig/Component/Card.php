<?php

declare(strict_types=1);

namespace Yu\EasyAdminLTEBundle\Twig\Component;

use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;
use Symfony\UX\TwigComponent\Attribute\PostMount;

/**
 * AdminLTE card with an optional colored header, header tools (collapse, maximize, remove) and footer.
 * The tools are handled by AdminLTE's JavaScript (data-lte-toggle="card-collapse|card-maximize|card-remove").
 *
 *     <twig:AdminLTE:Card title="Latest posts" variant="primary" outline collapsible>
 *         ...body...
 *         <twig:block name="tools">...extra header buttons...</twig:block>
 *         <twig:block name="footer">...</twig:block>
 *     </twig:AdminLTE:Card>
 */
#[AsTwigComponent]
final class Card
{
    use VariantTrait;

    /** header title; NULL renders no header unless there are tools */
    public ?string $title = null;
    /** one of VariantTrait::VARIANTS; NULL renders a neutral card */
    public ?string $variant = null;
    /** TRUE only colors the top border ("card-outline") instead of the whole header */
    public bool $outline = false;
    /** adds a collapse/expand button to the header */
    public bool $collapsible = false;
    /** initially collapsed (implies collapsible) */
    public bool $collapsed = false;
    /** adds a button that expands the card to the full screen */
    public bool $maximizable = false;
    /** adds a button that removes the card from the page (client side only) */
    public bool $removable = false;
    /** extra CSS classes of the body, e.g. "p-0" for an edge to edge table */
    public ?string $bodyClass = null;

    /**
     * Validates the props passed from the template and makes a collapsed card collapsible.
     *
     * @throws \InvalidArgumentException on an invalid variant
     */
    #[PostMount]
    public function validate(): void
    {
        $this->assertValidVariant($this->variant);

        if ($this->collapsed) {
            $this->collapsible = true;
        }
    }

    /**
     * Whether the header has any of the built-in tool buttons (collapse, maximize, remove).
     */
    public function hasTools(): bool
    {
        return $this->collapsible || $this->maximizable || $this->removable;
    }
}
