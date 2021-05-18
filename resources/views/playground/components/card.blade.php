<div class="ui  container p-y-2">
    <h2 class="ui header">
        Card
        <div class="sub header">
            Card adalah representasi khusus panel yang biasanya disajikan dalam bentuk grid.
            Card dalam satu row dijamin memiliki height yang sama.
        </div>
    </h2>
</div>

<x-volt-cards>
    <x-volt-card
            meta.before="<i class='icon coins yellow'></i> 100 poin"
            title="Increase confidence with TrustPilot reviews"
            content="Many people also have their own barometers for what makes a cute dog."
            url="#"
    >
        <x-slot name="body">
            <div class="content">
                <div class="description">
                    <x-volt-label label="foo" color="theme solid"></x-volt-label>
                    <x-volt-label label="bar" color="theme secondary"></x-volt-label>
                </div>
            </div>
        </x-slot>
        <x-volt-card-footer left='<i class="icon check"></i> 121 votes' right="right" />
    </x-volt-card>
    <x-volt-card
            meta.before="<i class='icon coins yellow'></i> 100 poin"
            title="Increase confidence with TrustPilot reviews"
            content="Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores atque culpa distinctio facilis minima neque quas. Consequatur delectus distinctio ducimus expedita minus mollitia odio officia pariatur quod, totam. Cumque, provident."
    >
        <x-volt-card-footer left='<i class="icon check"></i> 121 votes' right="right" />
    </x-volt-card>
    <x-volt-card
            meta.before="<i class='icon coins yellow'></i> 100 poin"
            title="Increase confidence with TrustPilot reviews"
            content="Many people also have their own barometers for what makes a cute dog."
    >
        <x-volt-card-footer left='<i class="icon check"></i> 121 votes' right="right" />
    </x-volt-card>
</x-volt-cards>

