<?php

declare(strict_types=1);

namespace Workbench\App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use ikhbarnuridev\FilamentMegaMenu\FilamentMegaMenuPlugin;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Workbench\App\Filament\Pages\Articles;
use Workbench\App\Filament\Pages\Changelog;
use Workbench\App\Filament\Pages\Overview;
use Workbench\App\Filament\Pages\Products;
use Workbench\App\Filament\Resources\UserResource;

/**
 * Sandbox panel. The groups exist to push the topbar past one row, which is the
 * only condition the plugin reacts to.
 *
 * A few pages are real and routable, so their navigation items carry the same
 * route-based active state a consumer's resources and pages do; the rest are
 * placeholders, there to keep the bar overflowing.
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
            ->pages([
                Dashboard::class,
                Overview::class,
                Changelog::class,
                Products::class,
                Articles::class,
            ])
            ->resources([UserResource::class])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ])
            ->plugin(FilamentMegaMenuPlugin::make()->label('Menu')->icon('heroicon-o-bars-3')->layout('horizontal')->columns(2)->itemColumns(3)->ungroupedColumns(4))
            ->navigation(fn (NavigationBuilder $builder): NavigationBuilder => $builder
                ->items([
                    ...Overview::getNavigationItems(),
                    ...Changelog::getNavigationItems(),
                    NavigationItem::make('Help')
                        ->url('/demo?item=help')
                        ->icon('heroicon-o-question-mark-circle'),
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
                    $this->group('Catalog', 'heroicon-o-cube', items: 3, leading: Products::getNavigationItems()),
                    $this->group('Sales', 'heroicon-o-shopping-cart', items: 2),
                    $this->group('Customers', 'heroicon-o-users', items: 6),
                    $this->group('Content', 'heroicon-o-document-text', items: 2, leading: Articles::getNavigationItems()),
                    $this->group('Reports', 'heroicon-o-chart-bar', items: 5),
                    $this->group('Inventory', 'heroicon-o-archive-box', items: 1),
                    // No group icon, so its items may carry their own.
                    $this->group('Settings', null, 'heroicon-o-cog-6-tooth', items: 3),
                ]));
    }

    /**
     * @param  array<NavigationItem>  $leading  Real items, placed ahead of the fillers.
     */
    protected function group(string $label, ?string $icon, ?string $itemIcon = null, int $items = 4, array $leading = []): NavigationGroup
    {
        return NavigationGroup::make($label)
            ->icon($icon)
            ->items([
                ...$leading,
                ...array_map(
                    fn (int $index): NavigationItem => NavigationItem::make("{$label} {$index}")
                        ->url('/demo?item='.$index)
                        ->icon($itemIcon),
                    range(1, $items),
                ),
            ]);
    }
}
