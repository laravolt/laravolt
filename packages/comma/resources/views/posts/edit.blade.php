@extends(
    config('laravolt.comma.view.layout'),
    [
        '__page' => [
            'title' => __('comma::post.header.edit'),
            'actions' => [
                [
                    'label' => __('Kembali'),
                    'class' => '',
                    'icon' => 'icon angle left',
                    'url' => route('comma::posts.index', $collection)
                ],
            ]
        ],
    ]
)

@section('content')
    {!! form()->bind($post)->open()->put()->route('comma::posts.update', $post) !!}
    {!! form()->text('title')->label(trans('comma::post.attributes.title'))->required() !!}
    {!! form()->selectMultiple('tags[]', $tags, $post->tag_array)->placeholder('')->label(trans('comma::post.attributes.tags')) !!}
    {!! form()->redactor('content')->label(trans('comma::post.attributes.content'))->required() !!}
    {!! form()->action(
        form()->submit(trans('comma::post.action.save'))->addClass('primary'),
        form()->link(trans('comma::post.action.cancel'), route('comma::posts.index', $collection))
    ) !!}
    {!! form()->close() !!}
@endsection
