@extends(config('laravolt.comma.view.layout'))

@section('content')

    <a href="{{ route('comma::posts.index') }}" class="ui button mini"><i class="icon left angle"></i> @lang('comma::menu.posts')
    </a>
    <h2 class="ui header">@lang('comma::post.header.edit')</h2>
    <div class="p-y-1">
        {!! SemanticForm::open()->put()->multipart()->route('comma::posts.update', $post->id) !!}
        <div class="ui grid">
            <div class="ui eleven wide column">
                {!! SemanticForm::text('title', $post->title)->label(trans('comma::post.attributes.title')) !!}
                {!! SemanticForm::textarea('content', $post->content)->label(trans('comma::post.attributes.content'))->id('postContent') !!}
            </div>
            <div class="ui five wide column">
                <div class="ui segments">
                    <div class="ui segment secondary">
                        {!! SemanticForm::select('category_id', $categories, $post->category_id)->label(trans('comma::post.attributes.category')) !!}
                        {!! SemanticForm::selectMultiple('tags[]', $tags, old('tags', $post->tagList))->placeholder('')->label(trans('comma::post.attributes.tags')) !!}
                    </div>
                    <div class="ui segment secondary">
                        {!! SemanticForm::file('featured_image')->id('featuredImage')->label(trans('comma::post.attributes.featured_image')) !!}
                        <img id="image" class="ui image" src="{{ asset($featuredImageUrl) }}">
                    </div>
                    <div class="ui segment actions secondary">
                        @if($post->isSession())
                            {!! SemanticForm::submit(trans('comma::post.action.publish'), 'action')->value('publish')->addClass('primary fluid') !!}
                            {!! SemanticForm::submit(trans('comma::post.action.save_as_draft'), 'action')->value('draft')->addClass('basic primary fluid') !!}
                        @elseif($post->isDraft())
                            {!! SemanticForm::submit(trans('comma::post.action.save'), 'action')->value('draft')->addClass('fluid') !!}
                            {!! SemanticForm::submit(trans('comma::post.action.publish'), 'action')->value('publish')->addClass('primary fluid') !!}
                        @elseif($post->isPublished())
                            {!! SemanticForm::submit(trans('comma::post.action.save'), 'action')->value('publish')->addClass('primary fluid') !!}
                            {!! SemanticForm::submit(trans('comma::post.action.unpublish'), 'action')->value('unpublish')->addClass('danger fluid') !!}
                        @elseif($post->isUnpublished())
                            {!! SemanticForm::submit(trans('comma::post.action.save'), 'action')->value('save')->addClass('primary fluid') !!}
                            {!! SemanticForm::submit(trans('comma::post.action.publish'), 'action')->value('publish')->addClass('fluid') !!}
                        @else
                            {!! SemanticForm::submit(trans('comma::post.action.save'), 'action')->value('save')->addClass('primary fluid') !!}
                        @endif
                    </div>
                </div>
            </div>
        </div>
        {!! SemanticForm::close() !!}

    </div>
@endsection

@push('head')
<link rel="stylesheet" href="{{ asset('lib/redactor/redactor.css') }}">
<link rel="stylesheet" href="{{ asset('lib/redactor/plugins/alignment/alignment.css') }}">
<style>
    body {
        overflow-x: initial;
    }

    .actions .button {
        margin-bottom: .5em;
    }
</style>
@endpush

@push('body')
<script src="{{ asset('lib/redactor/redactor.min.js') }}"></script>
<script src="{{ asset('lib/redactor/plugins/alignment/alignment.js') }}"></script>
<script src="{{ asset('lib/redactor/plugins/fontcolor.js') }}"></script>
<script src="{{ asset('lib/redactor/plugins/source.js') }}"></script>
<script src="{{ asset('lib/redactor/plugins/table.js') }}"></script>

<script>
    $(function () {
        $('#postContent').redactor({
            buttons: ['format', 'bold', 'italic', 'ol', 'ul', 'link', 'image'],
            plugins: ['source', 'table', 'alignment', 'fontcolor'],
            minHeight: 500,
            toolbarFixedTopOffset: 60,
            imageUpload: '{{ route('comma::media.store') }}',
            imageResizable: true,
            imagePosition: true,
            imageUploadFields: {
                '_token': '{{ csrf_token() }}',
                'post_id': '{{ $post->id }}'
            }
        });

        $("#featuredImage").change(function () {
            var reader = new FileReader();

            reader.onload = function (e) {
                $("#image").attr('src', e.target.result);
            };

            reader.readAsDataURL(this.files[0]);
        });

    });
</script>

@endpush
