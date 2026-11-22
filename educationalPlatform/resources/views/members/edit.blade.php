@extends('layouts.app')
@section('content')<h1 class="text-3xl font-bold">Edit member</h1><form method="POST" action="{{ route('members.update', $member) }}" class="mt-8 max-w-3xl space-y-6 rounded-xl bg-white p-6 shadow-sm">@method('PUT') @include('members.form', ['buttonText' => 'Save changes'])</form>@endsection
