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

    protected ?int $itemColumns = null;

    protected ?int $ungroupedColumns = null;

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

    /**
     * Columns used to lay out the navigation groups themselves.
     */
    public function columns(int $columns): static
    {
        $this->columns = max(1, $columns);

        return $this;
    }

    /**
     * Columns used for the items inside one group. Unset means `1` for the
     * `horizontal` layout and the group count for `vertical`.
     */
    public function itemColumns(int $itemColumns): static
    {
        $this->itemColumns = max(1, $itemColumns);

        return $this;
    }

    /**
     * Items shown per row in the groupless row above the groups. Unset lets
     * them wrap naturally.
     */
    public function ungroupedColumns(int $ungroupedColumns): static
    {
        $this->ungroupedColumns = max(1, $ungroupedColumns);

        return $this;
    }

    public function register(Panel $panel): void
    {
        $panel->renderHook(
            PanelsRenderHook::TOPBAR_LOGO_AFTER,
            fn (): string => view('filament-mega-menu::mega-menu', [
                'label' => $this->label ?? __('Menu'),
                'icon' => $this->icon,
                'layout' => $this->layout,
                'columnCount' => $this->columns,
                'itemColumnCount' => $this->itemColumns ?? ($this->layout === 'vertical' ? $this->columns : 1),
                'ungroupedColumnCount' => $this->ungroupedColumns,
            ])->render(),
        );
    }

    public function boot(Panel $panel): void {}
}
