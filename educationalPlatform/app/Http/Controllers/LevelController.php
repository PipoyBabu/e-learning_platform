<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLevelRequest;
use App\Http\Requests\UpdateLevelRequest;
use App\Models\MembershipLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LevelController extends Controller
{
    public function index(): View
    {
        return view('levels.index', ['levels' => MembershipLevel::withCount(['members', 'transactions'])->orderBy('fee')->get()]);
    }

    public function create(): View
    {
        return view('levels.create');
    }

    public function store(StoreLevelRequest $request): RedirectResponse
    {
        MembershipLevel::create($request->validated());

        return to_route('levels.index')->with('success', 'Level created successfully.');
    }

    public function show(MembershipLevel $level): View
    {
        return view('levels.show', ['level' => $level->load('members')]);
    }

    public function edit(MembershipLevel $level): View
    {
        return view('levels.edit', compact('level'));
    }

    public function update(UpdateLevelRequest $request, MembershipLevel $level): RedirectResponse
    {
        $level->update($request->validated());

        return to_route('levels.index')->with('success', 'Level updated successfully.');
    }

    public function destroy(MembershipLevel $level): RedirectResponse
    {
        if ($level->members()->exists() || $level->transactions()->exists()) {
            return back()->with('error', 'This level cannot be deleted because it is assigned to members or transactions.');
        }

        $level->delete();

        return to_route('levels.index')->with('success', 'Level deleted successfully.');
    }
}
