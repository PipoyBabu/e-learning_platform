@extends('layouts.app')
@section('content')<h1 class="text-3xl font-bold">Add membership level</h1><form method="POST" action="{{ route('levels.store') }}" class="mt-8 max-w-2xl space-y-6 rounded-xl bg-white p-6 shadow-sm">@include('levels.form', ['buttonText' => 'Create level'])</form>@endsection
