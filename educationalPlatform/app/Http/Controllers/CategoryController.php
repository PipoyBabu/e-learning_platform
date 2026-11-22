<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\MembershipCategory;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoryController extends Controller
{
    public function index(): View
    {
        return view('categories.index', ['categories' => MembershipCategory::withCount('members')->orderBy('name')->get()]);
    }

    public function create(): View
    {
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        MembershipCategory::create($request->validated());

        return to_route('categories.index')->with('success', 'Category created successfully.');
    }

    public function show(MembershipCategory $category): View
    {
        return view('categories.show', ['category' => $category->load('members')]);
    }

    public function edit(MembershipCategory $category): View
    {
        return view('categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, MembershipCategory $category): RedirectResponse
    {
        $category->update($request->validated());

        return to_route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(MembershipCategory $category): RedirectResponse
    {
        if ($category->members()->exists()) {
            return back()->with('error', 'This category cannot be deleted because it is assigned to members.');
        }

        $category->delete();

        return to_route('categories.index')->with('success', 'Category deleted successfully.');
    }
}
