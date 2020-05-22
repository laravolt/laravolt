@extends('laravolt::layouts.app')

@section('content')
    <x-titlebar title="Kitchen Sink"></x-titlebar>

    <x-panel title="Typography">
        <h1 class="ui header">Heading 1</h1>
        <h2 class="ui header">Heading 2</h2>
        <h3 class="ui header">Heading 3</h3>
        <h4 class="ui header">Heading 4</h4>
        <h5 class="ui header">Heading 5</h5>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequuntur, doloribus fugit inventore quaerat
            quas quis ratione saepe sint totam. Distinctio laborum praesentium sit tempore voluptatem. Aspernatur odio
            provident repellat voluptate.</p>
        <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequuntur, doloribus fugit inventore quaerat
            quas quis ratione saepe sint totam. Distinctio laborum praesentium sit tempore voluptatem. Aspernatur odio
            provident repellat voluptate.</p>

        <div class="ui divider section"></div>

        <div class="ui container text">
            <h1 class="ui header">Text Container</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Consequuntur corporis cumque distinctio dolore
                ea enim et expedita in nobis nulla odit possimus quae quam quas quos reiciendis tempore temporibus,
                veritatis!</p>
        </div>

        <div class="ui divider section"></div>
        <div class="ui right floated horizontal list">
            <div class="disabled item" href="#">© GitHub, Inc.</div>
            <a class="item" href="#">Terms</a>
            <a class="item" href="#">Privacy</a>
            <a class="item" href="#">Contact</a>
        </div>
        <div class="ui horizontal list">
            <a class="item" href="#">About Us</a>
            <a class="item" href="#">Jobs</a>
        </div>

    </x-panel>

    <x-panel title="Button">
        <x-button label="Primary Button" type="primary"></x-button>
        <x-button label="Secondary Button" type="secondary"></x-button>
        <x-button label="Basic Button" type="basic"></x-button>

        <div class="ui divider section"></div>

        <div class="ui horizontal list">
            @foreach(config('laravolt.ui.colors') as $color => $hex)
                <div class="item">
                    <x-button type="{{ $color }}">{{ $color }}</x-button>
                </div>
            @endforeach
        </div>
        <div class="ui horizontal list">
            @foreach(config('laravolt.ui.colors') as $color => $hex)
                <div class="item">
                    <x-button type="{{ $color }} secondary">{{ $color }}</x-button>
                </div>
            @endforeach
        </div>
        <div class="ui horizontal list">
            @foreach(config('laravolt.ui.colors') as $color => $hex)
                <div class="item">
                    <x-button type="{{ $color }} basic">{{ $color }}</x-button>
                </div>
            @endforeach
        </div>
    </x-panel>


    <x-panel title="Label">
        <div class="ui horizontal list">
            @foreach(config('laravolt.ui.colors') as $color => $hex)
                <div class="item">
                    <x-label :color="$color">{{ $color }}</x-label>
                </div>
            @endforeach
        </div>
        <div class="ui horizontal list">
            @foreach(config('laravolt.ui.colors') as $color => $hex)
                <div class="item">
                    <x-label :color="'basic '.$color">{{ $color }}</x-label>
                </div>
            @endforeach
        </div>
    </x-panel>

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

    <div class="ui grid equal width">
        <div class="column">
            <x-panel title="Horizontal Form">
                {!! form()->get()->horizontal() !!}
                {!! form()->text('nama')->label('Nama') !!}
                {!! form()->dropdown('lokasi', ['Indonesia', 'Malaysia'])->label('Lokasi') !!}
                {!! form()->action(form()->submit('Simpan')) !!}
                {!! form()->close() !!}
            </x-panel>
        </div>
        <div class="column">
            <x-panel title="Vertical Form">
                {!! form()->get() !!}
                {!! form()->text('nama')->label('Nama') !!}
                {!! form()->dropdown('lokasi', ['Indonesia', 'Malaysia'])->label('Lokasi') !!}
                {!! form()->submit('Simpan') !!}
                {!! form()->action(form()->close()) !!}
            </x-panel>
        </div>
    </div>

    <div class="ui grid equal width">
        <div class="column">
            <x-panel title="Data">
                <table class="ui table definition">
                    <caption>Profil</caption>
                    <tbody>
                    <tr><td>Nama</td><td>Bayu Hendra</td></tr>
                    <tr><td>Posisi</td><td>Programmer</td></tr>
                    <tr><td>Lokasi</td><td>Indonesia</td></tr>
                    <tr><td>Bio</td><td>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Architecto ipsum nisi placeat possimus tenetur. Ad dicta dolores ducimus natus nihil officiis repellat ullam! Facere laboriosam necessitatibus pariatur quae qui rem!</td></tr>
                    </tbody>
                </table>

                <table class="ui table definition">
                    <caption>Profil</caption>
                    <tbody>
                    <tr><td>Nama</td><td>Bayu Hendra</td></tr>
                    <tr><td>Posisi</td><td>Programmer</td></tr>
                    <tr><td>Lokasi</td><td>Indonesia</td></tr>
                    <tr><td>Bio</td><td>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Architecto ipsum nisi placeat possimus tenetur. Ad dicta dolores ducimus natus nihil officiis repellat ullam! Facere laboriosam necessitatibus pariatur quae qui rem!</td></tr>
                    </tbody>
                </table>
            </x-panel>
        </div>
    </div>

    <div class="ui divider hidden"></div>
    {!! \Modules\Mission\Tables\IndexTableView::make()->title('Tabel')->render() !!}
@endsection
