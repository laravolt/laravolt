@extends(config('laravolt.comma.view.layout'))

@section('content')

    <div class="ui grid two column">
        <div class="column"><h2 class="ui header">@lang('comma::post.header.index')</h2></div>
        <div class="column right aligned"><a href="{{ route('comma::posts.create') }}" class="ui button primary"><i class="icon plus"></i> @lang('comma::post.action.create')</a></div>
    </div>

    <div class="ui divider hidden"></div>

    {!! Suitable::source($posts)
    ->columns([
        ['header' => trans('comma::post.attributes.title'), 'field' => 'title'],
        ['header' => trans('comma::post.attributes.author'), 'field' => 'author.name'],
        ['header' => trans('comma::post.attributes.category'), 'field' => 'category.name'],
        ['header' => trans('comma::post.attributes.tags'), 'raw' => function($post){
            return $post->tag_list;
        }],
        ['header' => trans('comma::post.attributes.date'), 'field' => 'created_at'],
        with(new \Laravolt\Suitable\Columns\RestfulButton('comma::posts'))->only(['edit', 'delete'])
    ])
    ->render() !!}

@endsection
