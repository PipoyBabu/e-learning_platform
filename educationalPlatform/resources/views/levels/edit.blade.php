@extends('layouts.app')
@section('content')<h1 class="text-3xl font-bold">Edit membership level</h1><form method="POST" action="{{ route('levels.update', $level) }}" class="mt-8 max-w-2xl space-y-6 rounded-xl bg-white p-6 shadow-sm">@method('PUT') @include('levels.form', ['buttonText' => 'Save changes'])</form>@endsection
