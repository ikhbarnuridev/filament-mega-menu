# Changelog

All notable changes to this project are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.1] - 2026-11-08

### Fixed

- Panel labels stay readable when the panel repaints its topbar. A panel that
  colours the bar white (a gradient header, say) also coloured the panel's own
  links, which then disappeared on the white card. The panel now restates its
  colours against its own id, so a panel's rules no longer reach into it.

## [1.0.0] - 2026-11-08

First release.

### Added

- Mega menu panel for topbar navigation groups, opened from a normal topbar item
  so the topbar stays one row tall. The trigger stays hidden while every group
  still fits on one row.
- `FilamentMegaMenuPlugin` options: `label()`, `icon()`, `layout()`, `columns()`,
  `itemColumns()` and `ungroupedColumns()`.
- `horizontal` layout puts one navigation group per column; `vertical` stacks the
  groups and flows each group's items across the panel. The grid drops to a
  single column below 640px.
- Navigation items registered without a group get their own row above the grouped
  columns.
- Workbench sandbox panel with the full auth middleware stack, the user menu with
  its theme switcher, and a `UserResource` for the global search.

### Changed

- Columns are read from `filament()->getNavigation()`, so the menu always mirrors
  the panel's own resources and pages.
- Styling uses Filament's CSS variables and plain CSS scoped to `.fmm-*`, so no
  Tailwind `@source` step is needed and dark mode follows the panel.
- On a desktop-width screen the panel matches the width of the topbar navigation
  it hangs from; below that it spans the viewport.

[1.0.1]: https://github.com/ikhbarnuridev/filament-mega-menu/releases/tag/v1.0.1
[1.0.0]: https://github.com/ikhbarnuridev/filament-mega-menu/releases/tag/v1.0.0
