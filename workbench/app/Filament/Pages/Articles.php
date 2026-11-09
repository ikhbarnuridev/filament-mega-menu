<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Pages;

use Filament\Pages\Page;
use UnitEnum;

/**
 * Lives in a group the topbar has to fold into the mega menu, so the panel
 * itself shows the current page highlighted, not only the trigger. Iconless:
 * the group carries the icon.
 */
class Articles extends Page
{
    protected static string|UnitEnum|null $navigationGroup = 'Content';
}
