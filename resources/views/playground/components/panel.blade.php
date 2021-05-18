<h2 class="ui header m-b-2">
    Panel
    <div class="sub header">Semua konten yang merupakan satu kesatuan wajib dibungkus dalam sebuah panel</div>
</h2>

<div class="ui grid equal width">
    <div class="column">
        <x-volt-panel title="Panel">
            <div class="ui placeholder">
                <div class="image header">
                    <div class="line"></div>
                    <div class="line"></div>
                </div>
                <div class="paragraph">
                    <div class="line"></div>
                    <div class="line"></div>
                </div>
            </div>
        </x-volt-panel>
    </div>
    <div class="column">
        <x-volt-panel title="Panel With Icon" icon="rocket">
            <div class="ui placeholder">
                <div class="image header">
                    <div class="line"></div>
                    <div class="line"></div>
                </div>
                <div class="paragraph">
                    <div class="line"></div>
                    <div class="line"></div>
                </div>
            </div>
        </x-volt-panel>
    </div>
    <div class="row">
        <div class="column">
            <x-volt-panel title="Panel With Footer">
                <div class="ui placeholder">
                    <div class="image header">
                        <div class="line"></div>
                        <div class="line"></div>
                    </div>
                    <div class="paragraph">
                        <div class="line"></div>
                        <div class="line"></div>
                    </div>
                </div>
                <x-slot name="footer">
                    Footer
                </x-slot>
            </x-volt-panel>
        </div>
        <div class="column">
            <x-volt-panel title="Panel With Action">
                <div class="ui placeholder">
                    <div class="image header">
                        <div class="line"></div>
                        <div class="line"></div>
                    </div>
                    <div class="paragraph">
                        <div class="line"></div>
                        <div class="line"></div>
                    </div>
                </div>
                <x-slot name="action">
                    <x-volt-link-button url="#" icon="edit" class="mini">Edit</x-volt-link-button>
                </x-slot>
                <x-slot name="footer">
                    Footer
                </x-slot>
            </x-volt-panel>
        </div>

    </div>
</div>
