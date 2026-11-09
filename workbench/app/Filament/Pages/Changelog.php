<?php

declare(strict_types=1);

namespace Workbench\App\Filament\Pages;

use BackedEnum;
use Filament\Pages\Page;

/**
 * Second groupless page, so the topbar's own row holds more than one item that
 * can be the current page.
 */
class Changelog extends Page
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 2;
}
