@extends('layout')

@section('content-body')
    <div class="links">
        @foreach($links as $link)
            <p><a href="{{ $link['url'] }}">{{ $link['text'] }}</a></p>
        @endforeach
    </div>
@stop