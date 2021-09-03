<x-volt-panel :title="$this->title()">
    <div class="ui unstackable grid middle aligned">
        @if($this->icon())
            <div class="two wide column center aligned">
                <div class="ui label {{ $this->color() }} massive">
                    <x-volt-icon :name="$this->icon()"></x-volt-icon>
                </div>
            </div>
        @endif
        <div class="{{ $this->icon() ? 'fourteen' : '' }} wide column">
            <div class="ui statistic small" style="width: 100%">
                <div class="value">
                    {{ $this->value() }}
                </div>
                <div class="label">
                    <span class="ui {{ $this->color() }} text" style="font-size: .9em">
                        {{ $this->label() }}
                    </span>
                </div>
            </div>
        </div>
    </div>
</x-volt-panel>
