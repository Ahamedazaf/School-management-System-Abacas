<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Helpers\Qs;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Repositories\UserRepo;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    protected $user;

    public function __construct(UserRepo $user)
    {
        $this->user = $user;
    }

    public function index()
    {
        return redirect()->route('dashboard');
    }

    public function privacy_policy()
    {
        $data['app_name'] = config('app.name');
        $data['app_url'] = config('app.url');
        $data['contact_phone'] = Qs::getSetting('phone');
        return view('pages.other.privacy_policy', $data);
    }

    public function terms_of_use()
    {
        $data['app_name'] = config('app.name');
        $data['app_url'] = config('app.url');
        $data['contact_phone'] = Qs::getSetting('phone');
        return view('pages.other.terms_of_use', $data);
    }

    // dashboard for Support Admin and Support Team
    public function dashboard()
    {
        $d = [];

        if (Qs::userIsTeamSAT()) {
            $d['users'] = $this->user->getAll();
        }

        $d['total_amount'] = Payment::sum('total_amount');

        $d['total_paid_till_now'] = DB::table('payment_records')
            ->whereYear('created_at', Carbon::now()->year)
            ->whereMonth('created_at', '<=', Carbon::now()->month)
            ->sum('amt_paid');

        $d['pending_amount'] = $d['total_amount'] - $d['total_paid_till_now'];

        return view('pages.support_team.dashboard', $d);
    }
}
