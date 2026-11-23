<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberRequest;
use App\Http\Requests\UpdateMemberRequest;
use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MemberController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $members = Member::with(['category', 'level'])
            ->when($search !== '', fn ($query) => $query->where(fn ($query) => $query->where('first_name', 'like', "%{$search}%")->orWhere('last_name', 'like', "%{$search}%")->orWhere('email', 'like', "%{$search}%")))
            ->when($request->filled('category'), fn ($query) => $query->where('membership_category_id', $request->integer('category')))
            ->when($request->filled('level'), fn ($query) => $query->where('membership_level_id', $request->integer('level')))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('members.index', ['members' => $members, 'categories' => MembershipCategory::orderBy('name')->get(), 'levels' => MembershipLevel::orderBy('name')->get()]);
    }

    public function create(): View
    {
        return view('members.create', $this->formOptions());
    }

    public function store(StoreMemberRequest $request): RedirectResponse
    {
        $member = Member::create($request->validated());

        return to_route('members.show', $member)->with('success', 'Member profile created successfully.');
    }

    public function show(Member $member): View
    {
        return view('members.show', ['member' => $member->load(['category', 'level', 'transactions.payments'])]);
    }

    public function edit(Member $member): View
    {
        return view('members.edit', ['member' => $member, ...$this->formOptions()]);
    }

    public function update(UpdateMemberRequest $request, Member $member): RedirectResponse
    {
        $member->update($request->validated());

        return to_route('members.show', $member)->with('success', 'Member updated successfully.');
    }

    public function deactivate(Member $member): RedirectResponse
    {
        $member->update(['is_active' => false]);

        return back()->with('success', 'Member deactivated successfully.');
    }

    public function reactivate(Member $member): RedirectResponse
    {
        $member->update(['is_active' => true]);

        return back()->with('success', 'Member reactivated successfully.');
    }

    private function formOptions(): array
    {
        return ['categories' => MembershipCategory::orderBy('name')->get(), 'levels' => MembershipLevel::orderBy('name')->get()];
    }
}
