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
        // Always plain server-side: whether the current page's item lands in the
        // panel is only known once the bar has been measured.
        ->and($html)->toContain('class="fi-topbar-item fmm-trigger"')
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

it('holds the bar back until the trigger has been measured', function () {
    $html = (string) FilamentView::renderHook(PanelsRenderHook::TOPBAR_LOGO_AFTER);

    expect($html)->toContain('html:not(.fmm-ready) .fi-topbar-nav-groups')
        // Clipped to one row, or the hidden wrapped row still grows the topbar.
        ->and($html)->toContain('max-height: calc(var(--spacing, 0.25rem) * 9)')
        ->and($html)->toContain('overflow: hidden')
        // Invisible rather than clipped-but-painted: the last slot swaps labels.
        ->and($html)->toContain('visibility: hidden')
        // Revealed in a finally, so the early returns still release the bar.
        ->and($html)->toContain("document.documentElement.classList.add('fmm-ready')")
        // SPA navigation has to hide it again for the incoming page.
        ->and($html)->toContain('livewire:navigating');
});

it('lets the panel links follow the panel spa mode', function () {
    // Filament's own href helper, so a link navigates exactly like the bar's.
    $panel = Panel::make()
        ->id('spa')
        ->path('spa')
        ->topNavigation()
        ->spa()
        ->plugin(FilamentMegaMenuPlugin::make())
        ->navigation(fn (NavigationBuilder $builder): NavigationBuilder => $builder
            ->groups([
                NavigationGroup::make('Catalog')->items([
                    NavigationItem::make('Products')->url('/products'),
                ]),
            ]));

    Filament::registerPanel($panel);
    Filament::setCurrentPanel($panel);
    $panel->boot();

    $html = (string) FilamentView::renderHook(PanelsRenderHook::TOPBAR_LOGO_AFTER);

    expect($html)->toContain('href="/products" wire:navigate')
        ->and($html)->toContain('x-on:click="open = false"');
});

it('removes the bar beside the first row instead of folding it after paint', function () {
    $html = (string) FilamentView::renderHook(PanelsRenderHook::TOPBAR_LOGO_AFTER);

    // A debounce would leave the re-wrapped bar painted for its whole delay.
    expect($html)->toContain('x-on:resize.window="measure(); measureBounds()"')
        ->and($html)->not->toContain('resize.window.debounce');
});

it('lights the trigger up only for an item that landed in the panel', function () {
    // The current page is known at render time, but the panel is not: the bar
    // measures which items it can keep, and an item it keeps already marks
    // itself active in the bar. So the item is marked server-side and the
    // trigger follows the measured panel.
    $panel = Panel::make()
        ->id('active')
        ->path('active')
        ->topNavigation()
        ->plugin(FilamentMegaMenuPlugin::make())
        ->navigation(fn (NavigationBuilder $builder): NavigationBuilder => $builder
            ->groups([
                NavigationGroup::make('Catalog')->items([
                    NavigationItem::make('Products')
                        ->url('/active/products')
                        ->activeWhen(fn (): bool => true),
                ]),
            ]));

    Filament::registerPanel($panel);
    Filament::setCurrentPanel($panel);
    $panel->boot();

    $html = (string) FilamentView::renderHook(PanelsRenderHook::TOPBAR_LOGO_AFTER);

    expect($html)->toContain('fmm-link fmm-active')
        // The item is marked server-side; the trigger follows the measured panel.
        ->and($html)->toContain("this.\$root.classList.toggle('fi-active', currentPageInPanel)")
        // An ungrouped item the bar kept is dropped from the panel's own copy.
        ->and($html)->toContain('inBar.has(link.textContent.trim())');
});
