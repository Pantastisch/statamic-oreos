@extends('statamic::layout')
@section('title', $title)

@section('content')

    <publish-form
        title="{{ __('oreos::messages.title') }}"
        action="{{ $action }}"
        :blueprint='@json($blueprint)'
        :meta='@json($meta)'
        :values='@json($values)'
    ></publish-form>

@endsection
