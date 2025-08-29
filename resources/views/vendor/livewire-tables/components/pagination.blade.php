@aware(['isTailwind','isBootstrap','isBootstrap4', 'isBootstrap5', 'localisationPath'])
@props(['currentRows'])

@includeWhen(
    $this->hasConfigurableAreaFor('before-pagination'), 
    $this->getConfigurableAreaFor('before-pagination'), 
    $this->getParametersForConfigurableArea('before-pagination')
)

<div {{ $this->getPaginationWrapperAttributesBag() }}>
    @if ($this->paginationVisibilityIsEnabled())
        @php
            // Tambah style global kecil
            $paginationWrapperClass = $isTailwind
                ? 'mt-4 px-4 md:p-0 sm:flex justify-between items-center space-y-4 sm:space-y-0'
                : 'row mt-3';
            $paginationTextClass = $isTailwind
                ? 'text-sm text-gray-700 leading-5'
                : 'text-muted small';
        @endphp

        @if ($isTailwind)
            <div class="{{ $paginationWrapperClass }}">
                <div>
                    @if ($this->paginationIsEnabled && $this->isPaginationMethod('standard') && $currentRows->lastPage() > 1 && $this->showPaginationDetails)
                        <p class="paged-pagination-results {{ $paginationTextClass }}">
                            {{ __($localisationPath.'Showing') }}
                            <span class="font-medium">{{ $currentRows->firstItem() }}</span>
                            {{ __($localisationPath.'to') }}
                            <span class="font-medium">{{ $currentRows->lastItem() }}</span>
                            {{ __($localisationPath.'of') }}
                            <span class="font-medium"><span x-text="paginationTotalItemCount"></span></span>
                            {{ __($localisationPath.'results') }}
                        </p>
                    @elseif ($this->paginationIsEnabled && $this->isPaginationMethod('simple') && $this->showPaginationDetails)
                        <p class="{{ $paginationTextClass }}">
                            {{ __($localisationPath.'Showing') }}
                            <span class="font-medium">{{ $currentRows->firstItem() }}</span>
                            {{ __($localisationPath.'to') }}
                            <span class="font-medium">{{ $currentRows->lastItem() }}</span>
                        </p>
                    @elseif(!$this->isPaginationMethod('cursor') && $this->showPaginationDetails)
                        <p class="total-pagination-results {{ $paginationTextClass }}">
                            {{ __($localisationPath.'Showing') }}
                            <span class="font-medium">{{ $currentRows->count() }}</span>
                            {{ __($localisationPath.'results') }}
                        </p>
                    @endif
                </div>

                {{-- Compact pagination links --}}
                @if ($this->paginationIsEnabled)
                    <div class="text-sm">
                        {{ $currentRows->links('livewire-tables::specific.tailwind.'.(!$this->isPaginationMethod('standard') ? 'simple-' : '').'pagination') }}
                    </div>
                @endif
            </div>
        @else
            <div class="{{ $paginationWrapperClass }}">
                <div class="col-12 col-md-6 overflow-auto">
                    {{-- Gunakan pagination link sesuai metode --}}
                    {{ $this->paginationIsEnabled 
                        ? $currentRows->links('livewire-tables::specific.bootstrap-4'.(!$this->isPaginationMethod('standard') ? '.simple' : '').'-pagination') 
                        : '' 
                    }}
                </div>

                <div @class([
                    "col-12 col-md-6 text-center $paginationTextClass",
                    "text-md-right" => $isBootstrap4,
                    "text-md-end" => $isBootstrap5,
                ])>
                    @if($this->showPaginationDetails)
                        <span>{{ __($localisationPath.'Showing') }}</span>
                        <strong>{{ $currentRows->count() ? $currentRows->firstItem() : 0 }}</strong>
                        @if(!$this->isPaginationMethod('cursor'))
                            <span>{{ __($localisationPath.'to') }}</span>
                            <strong>{{ $currentRows->count() ? $currentRows->lastItem() : 0 }}</strong>
                            <span>{{ __($localisationPath.'of') }}</span>
                            <strong><span x-text="paginationTotalItemCount"></span></strong>
                        @endif
                        <span>{{ __($localisationPath.'results') }}</span>
                    @endif
                </div>
            </div>
        @endif
    @endif
</div>

@includeWhen(
    $this->hasConfigurableAreaFor('after-pagination'), 
    $this->getConfigurableAreaFor('after-pagination'), 
    $this->getParametersForConfigurableArea('after-pagination')
)
