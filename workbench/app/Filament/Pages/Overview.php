<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;

/**
 * A genuinely routable page, unlike the sandbox's placeholder items. Its
 * navigation item carries a route-based `activeWhen()`, so the topbar has
 * something that really becomes active and the mega menu trigger can mirror it.
 */
class Overview extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-home';

    protected static ?int $navigationSort = 1;
}
