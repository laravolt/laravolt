<div class="ui  container p-y-2">
    <h2 class="ui header">
        Card
        <div class="sub header">
            Card adalah representasi khusus panel yang biasanya disajikan dalam bentuk grid.
            Card dalam satu row dijamin memiliki height yang sama.
        </div>
    </h2>
</div>

<x-laravolt::cards>
    <x-laravolt::card
            meta.before="<i class='icon coins yellow'></i> 100 poin"
            title="Increase confidence with TrustPilot reviews"
            content="Many people also have their own barometers for what makes a cute dog."
            url="#"
    >
        <x-laravolt::slot name="body">
            <div class="content">
                <div class="description">
                    <x-laravolt::label label="foo" color="theme solid"></x-laravolt::label>
                    <x-laravolt::label label="bar" color="theme secondary"></x-laravolt::label>
                </div>
            </div>
        </x-laravolt::slot>
        <x-laravolt::card-footer left='<i class="icon check"></i> 121 votes' right="right" />
    </x-laravolt::card>
    <x-laravolt::card
            meta.before="<i class='icon coins yellow'></i> 100 poin"
            title="Increase confidence with TrustPilot reviews"
            content="Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores atque culpa distinctio facilis minima neque quas. Consequatur delectus distinctio ducimus expedita minus mollitia odio officia pariatur quod, totam. Cumque, provident."
    >
        <x-laravolt::card-footer left='<i class="icon check"></i> 121 votes' right="right" />
    </x-laravolt::card>
    <x-laravolt::card
            meta.before="<i class='icon coins yellow'></i> 100 poin"
            title="Increase confidence with TrustPilot reviews"
            content="Many people also have their own barometers for what makes a cute dog."
    >
        <x-laravolt::card-footer left='<i class="icon check"></i> 121 votes' right="right" />
    </x-laravolt::card>
</x-laravolt::cards>

