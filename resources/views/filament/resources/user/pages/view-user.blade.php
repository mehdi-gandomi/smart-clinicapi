<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-6">
        <div>
            {{ $this->infolist }}
        </div>

        <div>
            {{ $this->firstAssessmentInfolist }}
        </div>

        @if (count($this->relationManagers))
            <x-filament-panels::resources.relation-managers
                :active-manager="$activeRelationManager"
                :managers="$this->getRelationManagers()"
                :owner-record="$record"
                :page-class="static::class"
            />
        @endif
    </div>
</x-filament-panels::page>
