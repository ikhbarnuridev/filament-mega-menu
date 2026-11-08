<?php

declare(strict_types=1);

namespace Ikhbarnuri\FilamentMegaMenu;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class FilamentMegaMenuServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-mega-menu';

    public static string $viewNamespace = 'filament-mega-menu';

    public function configurePackage(Package $package): void
    {
        $package
            ->name(static::$name)
            ->hasViews(static::$viewNamespace);
    }
}
