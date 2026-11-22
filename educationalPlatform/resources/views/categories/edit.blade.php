@extends('layouts.app')
@section('content')<h1 class="text-3xl font-bold">Edit category</h1><form method="POST" action="{{ route('categories.update', $category) }}" class="mt-8 max-w-2xl space-y-6 rounded-xl bg-white p-6 shadow-sm">@method('PUT') @include('categories.form', ['buttonText' => 'Save changes'])</form>@endsection
