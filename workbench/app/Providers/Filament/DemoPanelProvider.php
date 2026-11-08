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
            ->plugin(FilamentMegaMenuPlugin::make()->label('Menu')->icon('heroicon-o-bars-3')->layout('horizontal')->columns(2)->itemColumns(3)->ungroupedColumns(4))
            ->navigation(fn (NavigationBuilder $builder): NavigationBuilder => $builder
                ->items([
                    NavigationItem::make('Overview')
                        ->url('/demo?item=overview')
                        ->icon('heroicon-o-home'),
                    NavigationItem::make('Help')
                        ->url('/demo?item=help')
                        ->icon('heroicon-o-question-mark-circle'),
                    NavigationItem::make('Changelog')
                        ->url('/demo?item=changelog')
                        ->icon('heroicon-o-document-text'),
                    NavigationItem::make('Support')
                        ->url('/demo?item=support')
                        ->icon('heroicon-o-lifebuoy'),
                    NavigationItem::make('Status')
                        ->url('/demo?item=status')
                        ->icon('heroicon-o-signal'),
                    NavigationItem::make('Roadmap')
                        ->url('/demo?item=roadmap')
                        ->icon('heroicon-o-map'),
                    NavigationItem::make('Feedback')
                        ->url('/demo?item=feedback')
                        ->icon('heroicon-o-chat-bubble-left-right'),
                    NavigationItem::make('Pricing')
                        ->url('/demo?item=pricing')
                        ->icon('heroicon-o-currency-dollar'),
                ])
                ->groups([
                    $this->group('Catalog', 'heroicon-o-cube'),
                    $this->group('Sales', 'heroicon-o-shopping-cart'),
                    $this->group('Customers', 'heroicon-o-users'),
                    $this->group('Content', 'heroicon-o-document-text'),
                    $this->group('Reports', 'heroicon-o-chart-bar'),
                    $this->group('Inventory', 'heroicon-o-archive-box'),
                    // No group icon, so its items may carry their own.
                    $this->group('Settings', null, 'heroicon-o-cog-6-tooth'),
                ]));
    }

    protected function group(string $label, ?string $icon, ?string $itemIcon = null): NavigationGroup
    {
        return NavigationGroup::make($label)
            ->icon($icon)
            ->items(array_map(
                fn (int $index): NavigationItem => NavigationItem::make("{$label} {$index}")
                    ->url('/demo?item='.$index)
                    ->icon($itemIcon),
                range(1, 4),
            ));
    }
}
