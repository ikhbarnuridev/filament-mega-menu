<?php

declare(strict_types=1);

namespace ikhbarnuridev\FilamentMegaMenu;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;

class FilamentMegaMenuPlugin implements Plugin
{
    protected ?string $label = null;

    protected ?string $icon = null;

    protected string $layout = 'horizontal';

    protected int $columns = 3;

    public static function make(): static
    {
        return app(static::class);
    }

    public function getId(): string
    {
        return 'filament-mega-menu';
    }

    public function label(string $label): static
    {
        $this->label = $label;

        return $this;
    }

    public function icon(?string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * `horizontal` lays the groups out side by side, `vertical` stacks them and
     * flows each group's items across the panel instead.
     */
    public function layout(string $layout): static
    {
        $this->layout = $layout === 'vertical' ? 'vertical' : 'horizontal';

        return $this;
    }

    public function columns(int $columns): static
    {
        $this->columns = max(1, $columns);

        return $this;
    }

    public function register(Panel $panel): void
    {
        $panel->renderHook(
            PanelsRenderHook::TOPBAR_LOGO_AFTER,
            fn(): string => view('filament-mega-menu::mega-menu', [
                'label' => $this->label ?? __('Menu'),
                'icon' => $this->icon,
                'layout' => $this->layout,
                'columnCount' => $this->columns,
            ])->render(),
        );
    }

    public function boot(Panel $panel): void {}
}
