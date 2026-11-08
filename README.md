# Filament Mega Menu

Turns topbar navigation groups that overflow onto a second row into a full-width
mega menu, keeping the topbar one row tall.

The trigger is a normal topbar item, so it lines up with the other menu items. If
everything fits on one row, the trigger stays hidden and nothing changes.

## Install

Not on Packagist yet. Use a path repository:

```json
"repositories": [
    { "type": "path", "url": "../filament-mega-menu" }
]
```

```bash
composer require ikhbarnuridev/filament-mega-menu
```

## Usage

Register the plugin on any panel that uses top navigation:

```php
use ikhbarnuridev\FilamentMegaMenu\FilamentMegaMenuPlugin;

public function panel(Panel $panel): Panel
{
    return $panel
        ->topNavigation()
        ->plugin(FilamentMegaMenuPlugin::make());
}
```

The trigger label defaults to `Menu` (translatable). Override it:

```php
->plugin(FilamentMegaMenuPlugin::make()->label('More'))
```

## Notes

- Requires top navigation. Without `->topNavigation()` the topbar is not
  rendered at all, so the plugin has nothing to attach to.
- Columns are read from `filament()->getNavigation()`, so the menu always mirrors
  the panel's own resources and pages.
- Styling is plain CSS scoped to `.fmm-*` and uses Filament's CSS variables, so
  there is no Tailwind `@source` step and dark mode follows the panel.
