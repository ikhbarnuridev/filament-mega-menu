<?php

use Filament\Http\Middleware\Authenticate;
use Filament\Panel;
use Illuminate\Session\Middleware\StartSession;
use Workbench\App\Filament\Resources\UserResource;
use Workbench\App\Providers\Filament\DemoPanelProvider;

it('wires the demo panel like a real project', function () {
    $panel = (new DemoPanelProvider(app()))->panel(Panel::make());

    expect($panel->getMiddleware())->toContain(StartSession::class)
        ->and($panel->getAuthMiddleware())->toContain(Authenticate::class)
        ->and($panel->hasDarkMode())->toBeTrue()
        ->and($panel->hasThemeSwitcher())->toBeTrue()
        ->and($panel->hasUserMenu())->toBeTrue()
        ->and($panel->getResources())->toContain(UserResource::class);
});

it('makes the users resource globally searchable', function () {
    expect(UserResource::getGloballySearchableAttributes())->toBe(['name']);
});
