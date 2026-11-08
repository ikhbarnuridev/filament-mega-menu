<?php

declare(strict_types=1);

namespace Ikhbarnuri\FilamentMegaMenu;

use Filament\Contracts\Plugin;
use Filament\Panel;
use Filament\View\PanelsRenderHook;

class FilamentMegaMenuPlugin implements Plugin
{
    protected ?string $label = null;

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

    public function register(Panel $panel): void
    {
        $panel->renderHook(
            PanelsRenderHook::TOPBAR_LOGO_AFTER,
            fn (): string => view('filament-mega-menu::mega-menu', [
                'label' => $this->label ?? __('Menu'),
            ])->render(),
        );
    }

    public function boot(Panel $panel): void {}
}
