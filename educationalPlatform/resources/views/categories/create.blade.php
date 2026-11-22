@extends('layouts.app')
@section('content')<h1 class="text-3xl font-bold">Add category</h1><form method="POST" action="{{ route('categories.store') }}" class="mt-8 max-w-2xl space-y-6 rounded-xl bg-white p-6 shadow-sm">@include('categories.form', ['buttonText' => 'Create category'])</form>@endsection
