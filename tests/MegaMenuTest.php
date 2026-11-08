<?php

use Filament\Facades\Filament;
use Filament\Panel;
use Filament\Support\Facades\FilamentView;
use Filament\View\PanelsRenderHook;
use Ikhbarnuri\FilamentMegaMenu\FilamentMegaMenuPlugin;

beforeEach(function () {
    $panel = Panel::make()
        ->id('testing')
        ->default()
        ->path('testing')
        ->topNavigation()
        ->plugin(FilamentMegaMenuPlugin::make()->label('More'));

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
        ->and($html)->toContain('More');
});
