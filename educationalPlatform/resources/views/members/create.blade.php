@extends('layouts.app')
@section('content')<h1 class="text-3xl font-bold">Add member</h1><form method="POST" action="{{ route('members.store') }}" class="mt-8 max-w-3xl space-y-6 rounded-xl bg-white p-6 shadow-sm">@include('members.form', ['buttonText' => 'Create member'])</form>@endsection
