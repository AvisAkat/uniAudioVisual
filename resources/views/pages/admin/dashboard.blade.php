@extends('layouts.pages-layout')
@section('pageTitle', isset($pageTitle) ? $pageTitle : 'AVBMS | University Audio Visual Department')
@section('content')

    @livewire('admin.dashboard')

@endsection