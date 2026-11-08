<?php

use Filament\Facades\Filament;
use Filament\Navigation\NavigationBuilder;
use Filament\Navigation\NavigationGroup;
use Filament\Navigation\NavigationItem;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use ikhbarnuridev\FilamentMegaMenu\FilamentMegaMenuPlugin;

beforeEach(function () {
    $panel = Panel::make()
        ->id('testing')
        ->default()
        ->path('testing')
        ->topNavigation()
        ->plugin(FilamentMegaMenuPlugin::make()->label('More')->icon('heroicon-o-bars-3')->layout('vertical')->columns(4))
        ->navigation(fn (NavigationBuilder $builder): NavigationBuilder => $builder
            ->items([
                NavigationItem::make('Overview')->url('/overview')->icon('heroicon-o-home'),
                NavigationItem::make('Changelog')->url('/changelog'),
            ])
            ->groups([
                NavigationGroup::make('Catalog')->items([
                    NavigationItem::make('Products')->url('/products'),
                ]),
            ]));

    Filament::registerPanel($panel);
    Filament::setCurrentPanel($panel);
    $panel->boot();

    $this->panel = $panel;
});

it('injects a hidden trigger into the topbar logo slot', function () {
    $html = (string) FilamentView::renderHook(PanelsRenderHook::TOPBAR_LOGO_AFTER);

    expect($this->panel->hasPlugin('filament-mega-menu'))->toBeTrue()
        ->and($html)->toContain('fi-topbar-item fmm-trigger')
        ->and($html)->toContain('style="display: none"')
        ->and($html)->toContain('More')
        ->and($html)->toContain('fmm-grid fmm-grid-vertical')
        ->and($html)->toContain('--fmm-columns: 4')
        // Vertical defaults the item columns to the group count.
        ->and($html)->toContain('--fmm-item-columns: 4')
        // Trigger icon, chevron, and the one item icon.
        ->and(substr_count($html, '<svg'))->toBe(3);
});

it('renders groupless items as a row above the grouped columns', function () {
    $html = (string) FilamentView::renderHook(PanelsRenderHook::TOPBAR_LOGO_AFTER);

    $row = strpos($html, 'data-mega-ungrouped');
    $grid = strpos($html, 'class="fmm-grid');

    expect($row)->not->toBeFalse()
        ->and($grid)->not->toBeFalse()
        ->and($row)->toBeLessThan($grid)
        ->and($html)->toContain('data-mega-label=""')
        ->and($html)->toContain('Overview')
        ->and($html)->toContain('Changelog')
        ->and($html)->toContain('data-mega-label="Catalog"')
        ->and($html)->toContain('Products');
});

it('separates the group columns from the item columns', function () {
    $panel = Panel::make()
        ->id('items')
        ->path('items')
        ->topNavigation()
        ->plugin(FilamentMegaMenuPlugin::make()->layout('horizontal')->columns(2)->itemColumns(3));

    Filament::registerPanel($panel);
    Filament::setCurrentPanel($panel);
    $panel->boot();

    $html = (string) FilamentView::renderHook(PanelsRenderHook::TOPBAR_LOGO_AFTER);

    expect($html)->toContain('--fmm-columns: 2')
        ->and($html)->toContain('--fmm-item-columns: 3');
});

it('sets how many groupless items share a row', function () {
    $panel = Panel::make()
        ->id('ungrouped')
        ->path('ungrouped')
        ->topNavigation()
        ->plugin(FilamentMegaMenuPlugin::make()->ungroupedColumns(2))
        ->navigation(fn (NavigationBuilder $builder): NavigationBuilder => $builder->items([
            NavigationItem::make('Overview')->url('/overview'),
        ]));

    Filament::registerPanel($panel);
    Filament::setCurrentPanel($panel);
    $panel->boot();

    $html = (string) FilamentView::renderHook(PanelsRenderHook::TOPBAR_LOGO_AFTER);

    expect($html)->toContain('fmm-ungrouped-grid')
        ->and($html)->toContain('--fmm-ungrouped-columns: 2');
});
