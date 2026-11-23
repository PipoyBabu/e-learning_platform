<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMemberApplicationRequest;
use App\Models\Member;
use App\Models\MembershipCategory;
use App\Models\MembershipLevel;
use App\Notifications\MembershipApplicationSubmitted;
use App\Services\TransactionService;
use App\Exceptions\TransactionRuleException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Illuminate\View\View;

class MemberPortalController extends Controller
{
    public function __construct(private readonly TransactionService $transactionService) {}

    public function index(): View
    {
        $member = Member::with([
            'category',
            'level',
            'transactions' => fn ($query) => $query->with(['level', 'payments'])->latest(),
        ])->where('email', Auth::user()->email)->first();

        return view('member-portal.index', [
            'member' => $member,
            'levels' => MembershipLevel::orderBy('fee')->get(),
        ]);
    }

    public function editAccount(): View
    {
        return view('member-portal.account-edit', ['user' => Auth::user()]);
    }

    public function updateAccount(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'current_password' => ['nullable', 'required_with:password', 'current_password'],
            'password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $emailChanged = $data['email'] !== $user->email;
        $originalEmail = $user->email;

        DB::transaction(function () use ($user, $data, $originalEmail): void {
            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
                ...isset($data['password']) && $data['password'] !== ''
                    ? ['password' => Hash::make($data['password'])]
                    : [],
            ]);

            if ($member = Member::withTrashed()->where('email', $originalEmail)->first()) {
                $member->update(['email' => $data['email']]);
            }
        });

        if ($emailChanged) {
            $user->forceFill(['email_verified_at' => null])->save();
            $user->sendEmailVerificationNotification();

            return redirect()->route('verification.notice')->with('success', 'Your email was updated. Verify the new address to continue.');
        }

        return to_route('member.portal')->with('success', 'Your account details were updated.');
    }

    public function destroyAccount(Request $request): RedirectResponse
    {
        $request->validate(['current_password' => ['required', 'current_password']]);

        $user = $request->user();
        Auth::logout();
        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Your account has been deleted.');
    }

    public function createApplication(): View|RedirectResponse
    {
        if (Member::where('email', Auth::user()->email)->exists()) {
            return to_route('member.portal');
        }

        return view('member-portal.apply', [
            'categories' => MembershipCategory::orderBy('name')->get(),
            'levels' => MembershipLevel::orderBy('fee')->get(),
        ]);
    }

    public function storeApplication(StoreMemberApplicationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $member = Member::firstOrCreate(
            ['email' => $request->user()->email],
            [
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'] ?? null,
                'membership_category_id' => $data['membership_category_id'],
                'membership_level_id' => $data['membership_level_id'],
                'joined_on' => now()->toDateString(),
            ],
        );

        $level = MembershipLevel::findOrFail($data['membership_level_id']);

        try {
            $transaction = $this->transactionService->createApplication($member, $level);
        } catch (TransactionRuleException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        Notification::route('mail', $member->email)
            ->notify(new MembershipApplicationSubmitted($transaction->load('member', 'level')));

        return to_route('transactions.show', $transaction)->with('success', 'Your membership application was created. You can now make a payment.');
    }
}
