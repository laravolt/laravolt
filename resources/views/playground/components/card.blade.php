<div class="container mx-auto py-2">
    <h2 class="text-xl font-semibold text-gray-800">
        Card
        <span class="block text-sm font-normal text-gray-500">
            Card adalah representasi khusus panel yang biasanya disajikan dalam bentuk grid.
            Card dalam satu row dijamin memiliki height yang sama.
        </span>
    </h2>
</div>

<x-volt-cards>
    <x-volt-card
            meta.before="<span class='inline-flex items-center rounded-md bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800'>100 poin</span>"
            title="Increase confidence with TrustPilot reviews"
            content="Many people also have their own barometers for what makes a cute dog."
            url="#"
    >
        <x-slot name="body">
            <div class="px-4 py-3">
                <div class="text-sm text-gray-700">
                    <x-volt-label label="foo" color="theme solid"></x-volt-label>
                    <x-volt-label label="bar" color="theme secondary"></x-volt-label>
                </div>
            </div>
        </x-slot>
        <x-volt-card-footer left='<svg class="h-4 w-4 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> 121 votes' right="right" />
    </x-volt-card>
    <x-volt-card
            meta.before="<span class='inline-flex items-center rounded-md bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800'>100 poin</span>"
            title="Increase confidence with TrustPilot reviews"
            content="Lorem ipsum dolor sit amet, consectetur adipisicing elit. Asperiores atque culpa distinctio facilis minima neque quas. Consequatur delectus distinctio ducimus expedita minus mollitia odio officia pariatur quod, totam. Cumque, provident."
    >
        <x-volt-card-footer left='<svg class="h-4 w-4 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> 121 votes' right="right" />
    </x-volt-card>
    <x-volt-card
            meta.before="<span class='inline-flex items-center rounded-md bg-yellow-100 px-2 py-0.5 text-xs font-medium text-yellow-800'>100 poin</span>"
            title="Increase confidence with TrustPilot reviews"
            content="Many people also have their own barometers for what makes a cute dog."
    >
        <x-volt-card-footer left='<svg class="h-4 w-4 inline" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg> 121 votes' right="right" />
    </x-volt-card>
</x-volt-cards>

