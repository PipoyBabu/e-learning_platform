<?php

namespace App\Http\Controllers;

use App\Enums\PaymentStatus;
use App\Enums\TransactionStatus;
use App\Models\Member;
use App\Models\MembershipTransaction;
use App\Models\Payment;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        return view('dashboard', [
            'totalMembers' => Member::count(),
            'activeMembers' => Member::where('is_active', true)->count(),
            'pendingTransactions' => MembershipTransaction::where('status', TransactionStatus::Pending)->count(),
            'totalCollected' => Payment::where('status', PaymentStatus::Succeeded)->sum('amount'),
            'recentTransactions' => MembershipTransaction::with(['member', 'level'])->latest()->limit(8)->get(),
        ]);
    }
}
