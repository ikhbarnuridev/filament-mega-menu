<?php

use Filament\Facades\Filament;
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
        ->plugin(FilamentMegaMenuPlugin::make()->label('More')->icon('heroicon-o-bars-3')->layout('vertical')->columns(4));

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
        // Trigger icon plus the chevron.
        ->and(substr_count($html, '<svg'))->toBe(2);
});
