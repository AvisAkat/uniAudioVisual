@extends('layouts.pages-layout')
@section('pageTitle', isset($pageTitle) ? $pageTitle : 'AVBMS | University Audio Visual Department')
@section('content')

    <section class="app-view is-entering" id="view-equipment" aria-label="Browse equipment">
        @livewire('student.browse-equipment')
    </section>

@endsection