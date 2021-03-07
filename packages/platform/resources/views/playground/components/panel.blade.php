<h2 class="ui header m-b-2">
    Panel
    <div class="sub header">Semua konten yang merupakan satu kesatuan wajib dibungkus dalam sebuah panel</div>
</h2>

<div class="ui grid equal width">
    <div class="column">
        <x-laravolt::panel title="Panel">
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
        </x-laravolt::panel>
    </div>
    <div class="column">
        <x-laravolt::panel title="Panel With Icon" icon="github">
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
        </x-laravolt::panel>
    </div>
    <div class="row">
        <div class="column">
            <x-laravolt::panel title="Panel With Footer">
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
                <x-laravolt::slot name="footer">
                    Footer
                </x-laravolt::slot>
            </x-laravolt::panel>
        </div>
        <div class="column">
            <x-laravolt::panel title="Panel With Action">
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
                <x-laravolt::slot name="action">
                    <x-laravolt::link url="#" icon="edit" class="mini">Edit</x-laravolt::link>
                </x-laravolt::slot>
                <x-laravolt::slot name="footer">
                    Footer
                </x-laravolt::slot>
            </x-laravolt::panel>
        </div>

    </div>
</div>
