<div class="ui grid equal width">
    <div class="column">
        <x-panel title="Panel">
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
        </x-panel>
    </div>
    <div class="column">
        <x-panel title="Panel With Icon" icon="github">
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
        </x-panel>
    </div>
    <div class="column">
        <x-panel title="Panel With Footer">
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
        </x-panel>
    </div>
</div>
