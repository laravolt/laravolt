@extends('laravolt::layouts.app')
@php(\Laravolt\Asset\AssetFacade::add('article-editor'))
@section('content')
    <x-titlebar title="Article Editor"></x-titlebar>

    <!-- element -->
    <textarea id="entry">
    <table class="ui table">
        <tbody>
        <tr>
            <td>
                table cell 2
            </td>
            <td>
                table cell 3
            </td>
        </tr>
        <tr>
            <td>
                table cell 4
            </td>
            <td>
                table cell 5<br>
            </td>
        </tr>
        </tbody>
    </table>
    <div class="grid">
        <div class="column column-2">
            <p>
                satu
            </p>
        </div>
        <div class="column column-2">
            <p>
                dua
            </p>
            <p>
            </p>
        </div>
        <div class="column column-2">
            <p>
                tiga
            </p>
            <p>
            </p>
        </div>
        <div class="column column-2">
            <p>
                empat
            </p>
        </div>
        <div class="column column-2">
            <p>
                lima
            </p>
        </div>
        <div class="column column-2">
            <p>
                enam
            </p>
        </div>
    </div>
    </textarea>




@endsection

@push('script')
    <script>
        ArticleEditor('#entry', {
            css: '{{ url('article-editor/css') }}/',
            classes: {
                'table': 'ui table',
            }
        });
    </script>
@endpush
