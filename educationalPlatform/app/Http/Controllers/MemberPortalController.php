<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class MemberPortalController extends Controller
{
    public function index(): View
    {
        $member = Member::with([
            'category',
            'level',
            'transactions' => fn ($query) => $query->with(['level', 'payments'])->latest(),
        ])->where('email', Auth::user()->email)->first();

        return view('member-portal.index', compact('member'));
    }
}
