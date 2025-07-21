@aware(['isTailwind', 'isBootstrap'])

<input
    wire:model.live{{ $this->getSearchOptions() }}="search"
    type="text"
    placeholder="{{ $this->getSearchPlaceholder() }}"
    {{
        $attributes->merge($this->getSearchFieldAttributes())
            ->class([
                // Tailwind (jika search aktif)
                'block w-full rounded-none rounded-l-md border-gray-300 shadow-sm sm:text-sm focus:ring-0 focus:border-gray-300 transition duration-150 ease-in-out' =>
                    $isTailwind && $this->hasSearch() && (($this->getSearchFieldAttributes()['default'] ?? true) || ($this->getSearchFieldAttributes()['default-styling'] ?? true)),

                // Tailwind (jika search nonaktif)
                'block w-full rounded-md border-gray-300 shadow-sm sm:text-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 transition duration-150 ease-in-out' =>
                    $isTailwind && !$this->hasSearch() && (($this->getSearchFieldAttributes()['default'] ?? true) || ($this->getSearchFieldAttributes()['default-styling'] ?? true)),

                // Padding ikon jika ada
                'pl-8 pr-4' => $this->hasSearchIcon,
                'pl-3 pr-3' => !$this->hasSearchIcon,

                // Bootstrap
                'form-control' => $isBootstrap && ($this->getSearchFieldAttributes()['default'] ?? true),
            ])
            ->except(['default', 'default-styling', 'default-colors'])
    }}
/>
