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

    public function dashboard()
    {
        $d = [];
        if (Qs::userIsTeamSAT()) {
            $d['users'] = $this->user->getAll();
        }

        // Fetch all students
        $students = DB::table('users')->where('user_type', 'student')->get();

        // Process payment info
        $studentsWithPayments = $students->map(function ($student) {
            $monthlyPaid = DB::table('payment_records')
                ->where('student_id', $student->id)
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('amt_paid');

            // $student->fee_demand  = 10000;
            $student->monthly_paid = $monthlyPaid;
            // $student->pending = $student->fee_demand - $monthlyPaid;

            // Payment status for each month
            $paidMonths = DB::table('payment_records')
                ->where('student_id', $student->id)
                ->selectRaw('MONTH(created_at) as month')
                ->pluck('month')
                ->toArray();

            $monthsStatus = [];
            foreach (range(1, 12) as $m) {
                $monthsStatus[$m] = in_array($m, $paidMonths) ? 'Paid' : 'Not Paid';
            }

            $student->months_status = $monthsStatus;
            return $student;
        });

        //  Show ONLY unpaid members for the current month
        $unpaidStudents = $studentsWithPayments->filter(function ($student) {
            return $student->monthly_paid <= 0; // Not paid anything this month
        });

        // Dashboard data
        $d['students'] = $unpaidStudents;
        $d['students_count'] = $unpaidStudents->count();
        $d['total_fee'] = $d['students_count'] ;
        $d['current_month_paid'] = $unpaidStudents->sum('monthly_paid');
        $d['pending_amount'] = $d['total_fee'] - $d['current_month_paid'];
        $d['total_amount'] = Payment::sum('total_amount');
        
  

        return view('pages.support_team.dashboard', $d);
    }
}
