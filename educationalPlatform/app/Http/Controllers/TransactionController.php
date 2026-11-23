<?php

namespace App\Http\Controllers;

use App\Enums\TransactionStatus;
use App\Exceptions\TransactionRuleException;
use App\Http\Requests\StoreApplicationRequest;
use App\Http\Requests\StoreRenewalRequest;
use App\Models\Member;
use App\Models\MembershipLevel;
use App\Models\MembershipTransaction;
use App\Services\TransactionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TransactionController extends Controller
{
    public function __construct(private readonly TransactionService $transactionService) {}

    public function index(Request $request): View
    {
        $status = $request->string('status')->toString();
        $transactions = MembershipTransaction::with(['member', 'level'])
            ->when($status !== '', fn ($query) => $query->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('transactions.index', ['transactions' => $transactions, 'statuses' => TransactionStatus::cases()]);
    }

    public function createApplication(): View
    {
        return view('transactions.create-application', ['members' => Member::where('is_active', true)->orderBy('last_name')->get(), 'levels' => MembershipLevel::orderBy('fee')->get()]);
    }

    public function storeApplication(StoreApplicationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        try {
            $transaction = $this->transactionService->createApplication(Member::findOrFail($data['member_id']), MembershipLevel::findOrFail($data['membership_level_id']));
        } catch (TransactionRuleException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return to_route('transactions.show', $transaction)->with('success', 'Membership application created.');
    }

    public function createRenewal(): View
    {
        return view('transactions.create-renewal', ['members' => Member::where('is_active', true)->with('level')->orderBy('last_name')->get()]);
    }

    public function storeRenewal(StoreRenewalRequest $request): RedirectResponse
    {
        $member = Member::findOrFail($request->validated('member_id'));
        try {
            $transaction = $this->transactionService->createRenewal($member);
        } catch (TransactionRuleException $exception) {
            return back()->withInput()->with('error', $exception->getMessage());
        }

        return to_route('transactions.show', $transaction)->with('success', 'Membership renewal created.');
    }

    public function show(MembershipTransaction $transaction): View
    {
        return view('transactions.show', ['transaction' => $transaction->load(['member', 'level', 'payments'])]);
    }

    public function approve(MembershipTransaction $transaction): RedirectResponse
    {
        try {
            $this->transactionService->approve($transaction);
        } catch (TransactionRuleException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Transaction approved successfully.');
    }

    public function cancel(MembershipTransaction $transaction): RedirectResponse
    {
        try {
            $this->transactionService->cancel($transaction);
        } catch (TransactionRuleException $exception) {
            return back()->with('error', $exception->getMessage());
        }

        return back()->with('success', 'Transaction cancelled successfully.');
    }
}
