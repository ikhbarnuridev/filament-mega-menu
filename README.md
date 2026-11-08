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

The trigger label defaults to `Menu` (translatable), and every option is
optional:

```php
->plugin(
    FilamentMegaMenuPlugin::make()
        ->label('More')                       // trigger text
        ->icon('heroicon-o-bars-3')           // trigger icon, before the label
        ->layout('vertical')                  // 'horizontal' (default) or 'vertical'
        ->columns(4)                          // columns for the groups, default 3
        ->itemColumns(2)                      // columns for the items inside a group
        ->ungroupedColumns(4)                 // groupless items per row, default wrap
)
```

`horizontal` puts one navigation group per column. `vertical` stacks the groups
and flows each group's items across the panel instead. Either way the grid drops
to a single column below 640px.

`columns()` and `itemColumns()` are independent: the first lays out the groups,
the second lays out the links inside each group. Left unset, `itemColumns()`
stacks the links under `horizontal` and matches `columns()` under `vertical`.

`ungroupedColumns()` controls the row of groupless items above the groups. Left
unset they wrap to fill the width.

## Development

The package ships a Workbench sandbox panel so the menu can be looked at in a
browser, with enough navigation groups to push the topbar past one row.

```bash
composer build   # publish Filament assets, migrate + seed the sandbox DB
composer serve   # http://127.0.0.1:8000/demo
composer lint    # format with Pint
```

Log in with `demo@example.com` / `password`. Narrow the window under ~1200px and
the overflowing groups collapse into the "More" trigger.

## Notes

- Requires top navigation. Without `->topNavigation()` the topbar is not
  rendered at all, so the plugin has nothing to attach to.
- Columns are read from `filament()->getNavigation()`, so the menu always mirrors
  the panel's own resources and pages.
- Navigation items registered without a group (a bare `NavigationItem` or a
  resource with no `$navigationGroup`) get their own row above the grouped
  columns, laid out horizontally and always wrapping onto one line per row.
- Icons are per item and per group, exactly as Filament declares them: a group
  with `->icon()` renders it in the heading and its items stay iconless, a group
  without one lets its items carry icons. Mixing icons and plain items in the
  same column keeps the labels aligned.
- Styling is plain CSS scoped to `.fmm-*` and uses Filament's CSS variables, so
  there is no Tailwind `@source` step and dark mode follows the panel.
- The trigger only appears once the groups actually overflow onto a second row.
  On a wide screen nothing overflows, so nothing shows — zoom in the browser
  (`Ctrl` + `+`, `Cmd` + `+`) to force it.
