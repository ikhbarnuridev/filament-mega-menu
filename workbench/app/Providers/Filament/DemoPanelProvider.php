<?php

declare(strict_types=1);

namespace Workbench\App\Providers\Filament;

use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use ikhbarnuridev\FilamentMegaMenu\FilamentMegaMenuPlugin;

/**
 * Sandbox panel. The groups exist to push the topbar past one row, which is the
 * only condition the plugin reacts to.
 */
class DemoPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('demo')
            ->path('demo')
            ->login()
            ->topNavigation()
            ->pages([Dashboard::class])
            ->plugin(FilamentMegaMenuPlugin::make()->label('Menu')->icon('heroicon-o-bars-3')->layout('vertical')->columns(4))
            ->navigation(fn(NavigationBuilder $builder): NavigationBuilder => $builder->groups([
                $this->group('Catalog', 'heroicon-o-cube'),
                $this->group('Sales', 'heroicon-o-shopping-cart'),
                $this->group('Customers', 'heroicon-o-users'),
                $this->group('Content', 'heroicon-o-document-text'),
                $this->group('Reports', 'heroicon-o-chart-bar'),
                $this->group('Inventory', 'heroicon-o-archive-box'),
                $this->group('Settings', 'heroicon-o-cog-6-tooth'),
            ]));
    }

    protected function group(string $label, string $icon): NavigationGroup
    {
        return NavigationGroup::make($label)
            ->icon($icon)
            ->items(array_map(
                fn(int $index): NavigationItem => NavigationItem::make("{$label} {$index}")
                    ->url('/demo?item=' . $index),
                range(1, 4),
            ));
    }
}
