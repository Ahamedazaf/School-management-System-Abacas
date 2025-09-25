<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Helpers\Qs;
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

    public function dashboard()
    {
        $d = [];
        if (Qs::userIsTeamSAT()) {
            $d['users'] = $this->user->getAll();
        }

        // All students
        $students = DB::table('users')->where('user_type', 'student')->get();

        // Map each student with payments info
        $studentsWithPayments = $students->map(function ($student) {
            $monthlyPaid = DB::table('payment_records')
                ->where('student_id', $student->id)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('amt_paid');

            $student->monthly_paid = $monthlyPaid;
            $student->fee_demand  = 10000;
            $student->pending     = $student->fee_demand - $monthlyPaid;

            // Months paid
            $paidMonths = DB::table('payment_records')
                ->where('student_id', $student->id)
                ->selectRaw('MONTH(created_at) as month')
                ->pluck('month')
                ->toArray();

            //status for 12 months
            $monthsStatus = [];
            foreach (range(1, 12) as $m) {
                $monthsStatus[$m] = in_array($m, $paidMonths) ? 'Paid' : 'Not Paid';
            }

            $student->months_status = $monthsStatus;

            return $student;
        });

        // Only unpaid-pending students
        $unpaidStudents = $studentsWithPayments->filter(function ($s) {
            return $s->pending > 0;
        });

        $d['students']        = $unpaidStudents; // only unpaid
        $d['students_count']  = $unpaidStudents->count();
        $d['total_fee']       = $d['students_count'] * 10000;
        $d['current_month_paid'] = $unpaidStudents->sum('monthly_paid');
        $d['pending_amount']  = $d['total_fee'] - $d['current_month_paid'];

        return view('pages.support_team.dashboard', $d);
    }
}
