@extends('layouts.pages-layout')
@section('pageTitle', isset($pageTitle) ? $pageTitle : 'Dashboard | AVBMS')
@section('content')
    
    @livewire('staff.inventory')

@endsection