@php
    // One column per navigation group; items are the panel's own resources and
    // pages, so the menu never drifts from the navigation it mirrors.
    $navigation = collect(filament()->getNavigation())
        ->map(fn ($group) => [
            'label' => (string) $group->getLabel(),
            'icon' => $group->getIcon(),
            'items' => collect($group->getItems())
                ->filter(fn ($item) => filled($item->getUrl()))
                ->map(fn ($item) => [
                    'label' => $item->getLabel(),
                    'url' => $item->getUrl(),
                    'icon' => $item->getIcon(),
                    'active' => $item->isActive(),
                ])
                ->values()
                ->all(),
        ])
        ->filter(fn (array $column): bool => filled($column['items']))
        ->values();

    // Filament gives groupless items an empty label; they get their own row
    // above the grid instead of a headingless column.
    $ungrouped = $navigation->first(fn (array $column): bool => $column['label'] === '');
    $columns = $navigation->reject(fn (array $column): bool => $column['label'] === '')->values();
@endphp

{{-- Plain CSS instead of Tailwind: the consumer's theme.css only scans its own
     views, so utility classes from a package view would never be compiled. --}}
<style data-navigate-track>
    .fmm-panel {
        position: fixed;
        inset-inline: 0;
        top: 4rem;
        z-index: 40;
        max-height: calc(100dvh - 4rem);
        overflow-y: auto;
        border-top: 1px solid var(--gray-200);
        background-color: #fff;
        box-shadow: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
    }

    .dark .fmm-panel {
        border-top-color: color-mix(in oklab, #fff 10%, transparent);
        background-color: var(--gray-900);
    }

    .fmm-grid {
        display: grid;
        grid-template-columns: minmax(0, 1fr);
        gap: 1.5rem 2rem;
        padding: 1.5rem;
    }

    /* Groupless items always flow in a single horizontal row. */
    .fmm-ungrouped {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.25rem 0.5rem;
        padding: 1rem 1.5rem 0;
    }

    /* A fixed count per row, when the panel asks for one. */
    .fmm-ungrouped-grid {
        display: grid;
        grid-template-columns: repeat(var(--fmm-ungrouped-columns, 1), minmax(0, 1fr));
        align-items: stretch;
    }

    @media (min-width: 640px) {
        .fmm-grid-horizontal {
            grid-template-columns: repeat(var(--fmm-columns, 3), minmax(0, 1fr));
        }

        .fmm-grid-vertical {
            grid-template-columns: minmax(0, 1fr);
        }
    }

    .fmm-heading {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--gray-200);
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--gray-950);
    }

    .dark .fmm-heading {
        border-bottom-color: color-mix(in oklab, #fff 10%, transparent);
        color: #fff;
    }

    .fmm-list {
        display: grid;
        grid-template-columns: repeat(var(--fmm-item-columns, 1), minmax(0, 1fr));
        gap: 0.125rem 0.5rem;
        margin: 0;
        padding: 0;
        list-style: none;
    }

    .fmm-link {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border-radius: 0.5rem;
        padding: 0.375rem 0.5rem;
        font-size: 0.875rem;
        color: var(--gray-600);
        text-decoration: none;
    }

    .fmm-link:hover {
        background-color: var(--gray-50);
    }

    /* Keep labels aligned when only some items in a column carry an icon. */
    .fmm-list:has(.fmm-link svg) .fmm-link:not(:has(svg)) {
        padding-inline-start: calc(0.5rem + 1.25rem + 0.5rem);
    }

    .dark .fmm-link {
        color: var(--gray-300);
    }

    .dark .fmm-link:hover {
        background-color: color-mix(in oklab, #fff 5%, transparent);
    }

    .fmm-link.fmm-active {
        font-weight: 500;
        color: var(--primary-600);
    }

    .fmm-link.fmm-active:hover {
        background-color: var(--primary-50);
    }

    .dark .fmm-link.fmm-active {
        color: var(--primary-400);
    }

    .dark .fmm-link.fmm-active:hover {
        background-color: color-mix(in oklab, var(--primary-400) 10%, transparent);
    }
</style>

{{-- A normal topbar item: it moves itself into the navigation row on boot, so it
     sits in line with the other menu items. The bar lets its groups wrap onto a
     second row; whatever wrapped moves in here instead, keeping the bar one row.
     With no overflow the item stays hidden. --}}
<li
    x-data="{
        open: false,
        nav: null,
        columns: [],

        init() {
            // One topbar per page, and this item lives inside it.
            this.nav = this.$root.closest('.fi-topbar')?.querySelector('.fi-topbar-nav-groups') ?? null;

            if (! this.nav) {
                return;
            }

            this.columns = Array.from(this.$root.querySelectorAll('[data-mega-column]'));

            // Rendered in the logo slot; move it in among the other items so it
            // lines up with them.
            this.nav.append(this.$root);

            this.measure();
            requestAnimationFrame(() => this.measure());
            document.fonts?.ready.then(() => this.measure());
        },

        measure() {
            const items = Array.from(this.nav.children).filter((child) => child !== this.$root);

            if (! items.length) {
                return;
            }

            // Measure the real items at natural width, this item hidden.
            this.$root.style.display = 'none';

            for (const item of items) {
                item.style.display = '';
            }

            // Below lg Filament swaps the topbar navigation for the sidebar, so
            // nothing can overflow and this item would only duplicate it.
            if (! this.nav.getClientRects().length) {
                this.open = false;

                return;
            }

            // Read the first row before hiding anything: a hidden item reports
            // top 0, which would drag the reference row to the very top.
            const rowTop = Math.min(...items.map((item) => item.getBoundingClientRect().top));
            const tolerance = items[0].getBoundingClientRect().height / 2;
            const inFirstRow = (element) => element.getBoundingClientRect().top - rowTop < tolerance;

            const firstWrapped = items.findIndex((item) => ! inFirstRow(item));

            if (firstWrapped === -1) {
                this.open = false;

                return;
            }

            for (const item of items.slice(firstWrapped)) {
                item.style.display = 'none';
            }

            // The item needs a slot of its own: drop items from the end until it
            // lands in the first row, not on the row it replaces.
            this.$root.style.display = '';

            while (! inFirstRow(this.$root)) {
                const last = items.filter((item) => item.style.display !== 'none').pop();

                if (! last) {
                    this.$root.style.display = 'none';
                    this.open = false;

                    return;
                }

                last.style.display = 'none';
            }

            // A column whose group is still in the first row would repeat it. Group
            // dropdown panels are teleported out of the bar, so their links are not
            // reachable from here -- match the visible labels instead.
            const inBar = new Set(
                items
                    .filter((item) => item.style.display !== 'none')
                    .map((item) => item.querySelector('.fi-topbar-item-label')?.textContent.trim())
                    .filter(Boolean),
            );

            for (const column of this.columns) {
                const label = column.dataset.megaLabel;

                // Ungrouped items have no heading; they repeat only when all of
                // their links are still in the bar.
                const shown = label
                    ? ! inBar.has(label)
                    : ! Array.from(column.querySelectorAll('a')).every((link) => inBar.has(link.textContent.trim()));

                column.style.display = shown ? '' : 'none';
            }
        },
    }"
    x-on:resize.window.debounce.150ms="measure()"
    x-on:keydown.escape.window="open = false"
    x-on:click.outside="open = false"
    style="display: none"
    class="fi-topbar-item fmm-trigger"
>
    <button
        type="button"
        x-on:click="open = ! open"
        x-bind:aria-expanded="open"
        aria-controls="filament-mega-menu"
        class="fi-topbar-item-btn"
    >
        @if ($icon)
            {{ \Filament\Support\generate_icon_html($icon) }}
        @endif

        <span class="fi-topbar-item-label">{{ $label }}</span>
        <x-filament::icon icon="heroicon-m-chevron-down" />
    </button>

    {{-- Full-width panel pinned under the sticky topbar; the grid drops to one
         column on narrow screens so the links stack downwards. --}}
    <div
        x-show="open"
        x-transition.opacity.duration.150ms
        id="filament-mega-menu"
        style="display: none"
        class="fmm-panel"
    >
        @if ($ungrouped)
            {{-- Groupless items: one horizontal row above the grouped columns. --}}
            <div
                data-mega-column
                data-mega-label=""
                data-mega-ungrouped
                @class([
                    'fmm-ungrouped',
                    'fmm-ungrouped-grid' => $ungroupedColumnCount !== null,
                ])
                @if ($ungroupedColumnCount !== null)
                    style="--fmm-ungrouped-columns: {{ $ungroupedColumnCount }}"
                @endif
            >
                @foreach ($ungrouped['items'] as $item)
                    <a
                        href="{{ $item['url'] }}"
                        x-on:click="open = false"
                        @class([
                            'fmm-link',
                            'fmm-active' => $item['active'],
                        ])
                    >
                        @if ($item['icon'])
                            {{ \Filament\Support\generate_icon_html($item['icon']) }}
                        @endif

                        {{ $item['label'] }}
                    </a>
                @endforeach
            </div>
        @endif

        <div class="fmm-grid fmm-grid-{{ $layout }}" style="--fmm-columns: {{ $columnCount }}; --fmm-item-columns: {{ $itemColumnCount }}">
            @foreach ($columns as $column)
                <div data-mega-column data-mega-label="{{ $column['label'] }}">
                    @if (filled($column['label']))
                        <p class="fmm-heading">
                            @if ($column['icon'])
                                {{ \Filament\Support\generate_icon_html($column['icon']) }}
                            @endif

                            {{ $column['label'] }}
                        </p>
                    @endif

                    <ul class="fmm-list">
                        @foreach ($column['items'] as $item)
                            <li>
                                <a
                                    href="{{ $item['url'] }}"
                                    x-on:click="open = false"
                                    @class([
                                        'fmm-link',
                                        'fmm-active' => $item['active'],
                                    ])
                                >
                                    @if ($item['icon'])
                                        {{ \Filament\Support\generate_icon_html($item['icon']) }}
                                    @endif

                                    {{ $item['label'] }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endforeach
        </div>
    </div>
</li>
