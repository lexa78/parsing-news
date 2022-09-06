@extends('main')

@section('content')
    <div class="container text-center">
        @foreach($news as $item)
            <div class="row">
                <div class="col">
                    <h3>{{ $item->title }}</h3>
                    <p>
                        @if(count($item->images) > 0)
                            <img width="300" height="186" src="{{ asset('storage/'.$item->images[0]->name) }}" alt="news picture" title="news picture">
                        @endif
                        <?= sprintf('%s...', mb_substr(strip_tags($item->news_text), 0, 200)) ?>
                    <p>
                        <small>Дата публикации {{ date('d.m.Y H:i:s', strtotime($item->news_datetime)) }}</small>
                        <a href="{{ route('allNewsFromCategory', ['id' => $item->category_id]) }}">#{{ $item->category->name }}</a>
                    </p>
                    <div style="align-content: center"><a href="{{ route('showWholeNews', ['id' => $item->id]) }}" class="btn btn-success">Читать всю новость</a></div>
                </div>
            </div>
            <hr>
        @endforeach
    </div>
@endsection
