@extends('main')

@section('content')
    <div class="container text-center">
        <div class="row">
            <div class="col">
                <p>
                    <small>Дата публикации {{ date('d.m.Y H:i:s', strtotime($news->news_datetime)) }}</small>
                    <a href="{{ route('allNewsFromCategory', ['id' => $news->category_id]) }}">#{{ $news->category->name }}</a>
                </p>
                <h2>{{ $news->title }}</h2>
                @if(count($news->images) > 0)
                    <p><img width="650" height="402" src="{{ asset('storage/'.$news->images[0]->name) }}" alt="news picture" title="news picture"></p>
                @endif
                <?= $news->news_text ?>
                <div style="align-content: center"><a href="{{ route('mainPage') }}" class="btn btn-info">Назад</a></div>
            </div>
        </div>
    </div>
@endsection
