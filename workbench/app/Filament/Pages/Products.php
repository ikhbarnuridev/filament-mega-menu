<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Pages;

use Filament\Pages\Page;
use UnitEnum;

/**
 * A grouped page, so the demo also proves an active item nested inside a panel
 * column lights the trigger up, not only the groupless row. Its group carries
 * the icon: Filament allows icons on either the group or its items, not both.
 */
class Products extends Page
{
    protected static string|UnitEnum|null $navigationGroup = 'Catalog';
}
