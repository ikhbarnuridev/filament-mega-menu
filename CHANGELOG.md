# Changelog

All notable changes to this project are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.3] - 2026-11-09

### Added

- The trigger is marked active when the current page is one of the items the
  panel actually holds. It reuses Filament's own `fi-active` class, so it wears
  the same look the topbar already gives an active navigation group: the label
  turns primary and the button takes a subtle background, in light and dark
  mode. An item the bar still fits is not repeated, so it does not light the
  trigger: it already marks itself active in the bar.
- The workbench sandbox gains four routable pages (`Overview`, `Changelog`,
  `Products`, `Articles`) with the route-based active state a real panel's pages
  and resources have, so the trigger's active state can be seen in the sandbox.

### Fixed

- The groupless row no longer repeats the items the topbar still shows. It used
  to appear whole as soon as one of its items spilled over, so the same item
  could sit in the bar and inside the menu at once.

## [1.0.2] - 2026-11-09

### Fixed

- The topbar no longer flashes its wrapped second row while the page loads. The
  bar is clipped to a single row and kept invisible until the trigger has been
  measured, so it appears once, already settled. `fmm-ready` is released in a
  `finally`, so an early return still reveals it, and it is dropped again on
  `livewire:navigating` for the incoming page.
- The topbar no longer changes height during a page load. The clipped bar keeps
  its wrapped row from occupying space.
- Resizing the window no longer paints the wrapped row during the transition.
  The resize handler is no longer debounced, so the bar is folded in the same
  frame it re-wrapped in.
- Panel links follow the panel's SPA mode. They now go through Filament's own
  `generate_href_html()`, so a panel with `->spa()` gets `wire:navigate` the same
  way the topbar's own links do.

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

[1.0.3]: https://github.com/ikhbarnuridev/filament-mega-menu/releases/tag/v1.0.3
[1.0.2]: https://github.com/ikhbarnuridev/filament-mega-menu/releases/tag/v1.0.2
[1.0.1]: https://github.com/ikhbarnuridev/filament-mega-menu/releases/tag/v1.0.1
[1.0.0]: https://github.com/ikhbarnuridev/filament-mega-menu/releases/tag/v1.0.0
