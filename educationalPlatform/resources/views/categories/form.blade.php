@csrf
<div><label class="font-medium">Name</label><input name="name" value="{{ old('name', $category->name ?? '') }}" class="mt-1 w-full rounded-lg border-slate-300 px-3 py-2.5 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200" required></div>
<div><label class="font-medium">Description</label><textarea name="description" rows="4" class="mt-1 w-full rounded-lg border-slate-300 px-3 py-2.5 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200">{{ old('description', $category->description ?? '') }}</textarea></div>
<button class="rounded-lg bg-indigo-600 px-4 py-2 font-semibold text-white hover:bg-indigo-700">{{ $buttonText }}</button>
