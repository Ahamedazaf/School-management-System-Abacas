<?php

namespace App\Http\Controllers\SupportTeam;

use PDF;
use App\Helpers\Qs;
use App\Helpers\Pay;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\PaymentRecord;
use App\Repositories\MyClassRepo;
use App\Repositories\PaymentRepo;
use App\Repositories\StudentRepo;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Payment\PaymentCreate;
use App\Http\Requests\Payment\PaymentUpdate;
use Illuminate\Validation\ValidationException;
use Throwable;
use App\Models\Fine;

class PaymentController extends Controller
{
    protected $my_class, $pay, $student, $year;

    public function __construct(MyClassRepo $my_class, PaymentRepo $pay, StudentRepo $student)
    {
        $this->my_class = $my_class;
        $this->pay = $pay;
        $this->year = Qs::getCurrentSession();
        $this->student = $student;

        $this->middleware('teamAccount')->except(['receipts', 'pdf_receipts']);
    }

    /* -------------------- Index / Show -------------------- */

    public function index()
    {
        $d['selected']   = true;
        $d['years']      = $this->pay->getPaymentYears();
        $d['payments']   = $this->pay->getPayment(['year' => $this->year])->get();
        $d['my_classes'] = $this->my_class->all();
        $d['year']       = $this->year;

        return view('pages.support_team.payments.index', $d);
    }

    public function show($year)
    {
        $d['payments'] = $p = $this->pay->getPayment(['year' => $year])->get();
        if ($p->count() < 1) {
            return Qs::goWithDanger('payments.index');
        }

        $d['selected']   = true;
        $d['my_classes'] = $this->my_class->all();
        $d['years']      = $this->pay->getPaymentYears();
        $d['year']       = $year;

        return view('pages.support_team.payments.index', $d);
    }

    public function select_year(Request $req)
    {
        return Qs::goToRoute(['payments.show', $req->year]);
    }

    public function create()
    {
        $d['my_classes'] = $this->my_class->all();
        $d['students']   = $this->student->getRecord([])->get()->sortBy('user.name');
        return view('pages.support_team.payments.create', $d);
    }

    // Fine And Invoice
    
public function invoice($st_id, $year = null)
{
    if (!$st_id) {
        return Qs::goWithDanger();
    }

    $inv            = $year ? $this->pay->getAllMyPR($st_id, $year) : $this->pay->getAllMyPR($st_id);
    $d['sr']        = $this->student->findByUserId($st_id)->first();
    $pr             = $inv->get();
    $d['uncleared'] = $pr->where('paid', 0);
    $d['cleared']   = $pr->where('paid', 1);

 
    $userId = $d['sr']->user_id; 

    $d['fines'] = Fine::where('user_id', $userId)->latest()->get();

    return view('pages.support_team.payments.invoice', $d);
}


    /* -------------------- Receipts -------------------- */

   public function receipts($pr_id)
{
    $pr = PaymentRecord::with(['receipt', 'payment'])->find($pr_id);
    if (!$pr) {
        return back()->with('flash_danger', 'Payment record not found.');
    }

    $d['pr']       = $pr;
    $d['receipts'] = $pr->receipt;
    $d['payment']  = $pr->payment;
    $d['sr']       = $this->student->findByUserId($pr->student_id)->first();

    $d['s'] = Setting::all()->flatMap(function ($s) {
        return [$s->type => $s->description ?? ''];
    });

    return view('pages.support_team.payments.receipt', $d);
}


    public function pdf_receipts($pr_id)
    {
        $pr = PaymentRecord::with(['receipt', 'payment'])->find($pr_id);
        if (!$pr) {
            return back()->with('flash_danger', 'Payment record not found.');
        }

        $d['pr']      = $pr;
        $d['receipts']= $pr->receipt;
        $d['payment'] = $pr->payment;
        $d['sr']      = $this->student->findByUserId($pr->student_id)->first();
        $d['s']       = Setting::all()->flatMap(fn($s) => [$s->type => $s->description]);

        $pdf_name = 'Receipt_' . $pr->ref_no . '.pdf';
        return PDF::loadView('pages.support_team.payments.receipt', $d)->download($pdf_name);
    }

    protected function downloadReceipt($page, $data, $name = null)
    {
        $path = 'receipts/file.html';
        $disk = Storage::disk('local');
        $disk->put($path, view($page, $data));
        $html = $disk->get($path);
        return PDF::loadHTML($html)->download($name);
    }

    public function pay_now(Request $req, $id)
    {
        try {
            if (!is_numeric($id)) {
                $id = Qs::decodeHash($id);
            }

            $validated = $req->validate([
                'months'   => ['required', 'array', 'min:1'],
                'months.*' => ['string']
            ]);

            $pr = $this->pay->findRecord($id);
            if (!$pr) {
                return response()->json(['ok' => false, 'msg' => 'Payment record not found.'], 404);
            }

            $payment = $this->pay->find($pr->payment_id);
            if (!$payment) {
                return response()->json(['ok' => false, 'msg' => 'Payment details not found.'], 404);
            }

            $alreadyPaidMonths = $pr->paid_months
                ? (is_array($pr->paid_months) ? $pr->paid_months : json_decode($pr->paid_months, true))
                : [];
            if (!is_array($alreadyPaidMonths)) {
                $alreadyPaidMonths = [];
            }

            $selectedMonths = collect($validated['months'])
                ->map(fn($m) => trim((string)$m))
                ->filter()
                ->unique()
                ->values()
                ->all();

            $newMonths = array_values(array_diff($selectedMonths, $alreadyPaidMonths));
            if (count($newMonths) === 0) {
                return response()->json(['ok' => false, 'msg' => 'No new months selected.'], 400);
            }

            $updatedMonths    = array_values(array_unique(array_merge($alreadyPaidMonths, $newMonths)));
            $monthlyAmount    = round($payment->amount / 12, 2);
            $newPaymentAmount = round($monthlyAmount * count($newMonths), 2);
            $currentPaid      = (float) ($pr->amt_paid ?? 0);
            $totalPaid        = round($currentPaid + $newPaymentAmount, 2);
            $balance          = round(max(0, $payment->amount - $totalPaid), 2);
            $fullyPaid        = count($updatedMonths) >= 12 ? 1 : 0;

            $this->pay->updateRecord($id, [
                'amt_paid'    => $totalPaid,
                'balance'     => $balance,
                'paid'        => $fullyPaid,
                'paid_months' => json_encode($updatedMonths),
                'updated_at'  => now(),
            ]);

            $this->pay->createReceipt([
                'amt_paid' => $newPaymentAmount,
                'balance'  => $balance,
                'pr_id'    => $id,
                'year'     => $this->year,
            ]);

            return response()->json([
                'ok'  => true,
                'msg' => 'Record Updated Successfully',
                'data'=> [
                    'amt_paid_now' => $newPaymentAmount,
                    'total_paid'   => $totalPaid,
                    'balance'      => $balance,
                    'paid_months'  => $updatedMonths,
                    'fully_paid'   => (bool)$fullyPaid,
                ],
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'ok'     => false,
                'msg'    => $e->getMessage(),
                'errors' => $e->errors()
            ], 422);
        } catch (Throwable $e) {
            return response()->json([
                'ok'    => false,
                'msg'   => 'Unexpected server error while processing payment.',
                'debug' => [
                    'message' => $e->getMessage(),
                    'line'    => $e->getLine(),
                    'file'    => $e->getFile(),
                ]
            ], 500);
        }
    }

    public function manage($class_id = null)
    {
        $d['my_classes'] = $this->my_class->all();
        $d['selected']   = false;

        if ($class_id) {
            $d['students'] = $st = $this->student
                ->getRecord(['my_class_id' => $class_id])
                ->get()
                ->sortBy('user.name');

            if ($st->count() < 1) {
                return Qs::goWithDanger('payments.manage');
            }
            $d['selected']   = true;
            $d['my_class_id']= $class_id;
        }

        return view('pages.support_team.payments.manage', $d);
    }

    public function select_class(Request $req)
    {
        $req->validate([
            'my_class_id' => 'required|exists:my_classes,id'
        ], [], ['my_class_id' => 'Class']);

        $wh['my_class_id'] = $class_id = $req->my_class_id;
        $pay1 = $this->pay->getPayment(['my_class_id' => $class_id, 'year' => $this->year])->get();
        $pay2 = $this->pay->getGeneralPayment(['year' => $this->year])->get();
        $payments = $pay2->count() ? $pay1->merge($pay2) : $pay1;
        $students = $this->student->getRecord($wh)->get();

        if ($payments->count() && $students->count()) {
            foreach ($payments as $p) {
                foreach ($students as $st) {
                    $pr = [
                        'student_id' => $st->user_id,
                        'payment_id' => $p->id,
                        'year'       => $this->year,
                    ];
                    $rec = $this->pay->createRecord($pr);
                    if (!$rec->ref_no) {
                        $rec->update(['ref_no' => mt_rand(100000, 99999999)]);
                    }
                }
            }
        }

        return Qs::goToRoute(['payments.manage', $class_id]);
    }

    public function store(PaymentCreate $req)
    {
        $data          = $req->all();
        $data['year']  = $this->year;
        $data['ref_no']= Pay::genRefCode();

        $payment  = $this->pay->create($data);
        $students = $this->student->getRecord([])->get();

        foreach ($students as $st) {
            $pr = [
                'student_id' => $st->user_id,
                'payment_id' => $payment->id,
                'year'       => $this->year,
            ];
            $record = $this->pay->createRecord($pr);
            if (!$record->ref_no) {
                $record->update(['ref_no' => mt_rand(100000, 99999999)]);
            }
        }

        return Qs::jsonStoreOk();
    }

    public function edit($id)
    {
        $d['payment'] = $pay = $this->pay->find($id);
        return is_null($pay) ? Qs::goWithDanger('payments.index') : view('pages.support_team.payments.edit', $d);
    }

    public function update(PaymentUpdate $req, $id)
    {
        $data = $req->all();
        $this->pay->update($id, $data);
        return Qs::jsonUpdateOk();
    }

    public function destroy($id)
    {
        $this->pay->find($id)->delete();
        return Qs::deleteOk('payments.index');
    }

    public function reset_record($id)
    {
        $this->pay->updateRecord($id, [
            'amt_paid'    => 0,
            'paid'        => 0,
            'balance'     => 0,
            'paid_months' => json_encode([]),
        ]);

        $this->pay->deleteReceipts(['pr_id' => $id]);
        return back()->with('flash_success', __('msg.update_ok'));
    }

    public function summary(Request $request)
    {
        $studentsQuery = DB::table('users')
            ->join('student_records', 'users.id', '=', 'student_records.user_id')
            ->where('users.user_type', 'student');

        if ($request->filled('class_id')) {
            $studentsQuery->where('student_records.my_class_id', $request->class_id);
        }

        $students = $studentsQuery->select('users.*', 'student_records.my_class_id')->get();

        $studentsWithPayments = $students->map(function ($student) {
            $monthlyPaid = DB::table('payment_records')
                ->where('student_id', $student->id)
                ->whereMonth('created_at', now()->month)
                ->whereYear('created_at', now()->year)
                ->sum('amt_paid');

            $student->monthly_paid = (float)$monthlyPaid;
            $student->fee_demand   = 10000;
            $student->pending      = $student->fee_demand - $student->monthly_paid;

            return $student;
        });

        $total_fee          = $studentsWithPayments->count() * 10000;
        $current_month_paid = $studentsWithPayments->sum('monthly_paid');
        $pending_amount     = $total_fee - $current_month_paid;

        return view('pages.support_team.payments.summary', [
            'total_fee'          => $total_fee,
            'current_month_paid' => $current_month_paid,
            'pending_amount'     => $pending_amount,
            'students'           => $studentsWithPayments
        ]);
    }

    public function fetchStudents(Request $request)
    {
        try {
            $classId = (int) $request->query('class_id', 0);
            $studentsQuery = $this->student->getRecord([])->with('user');

            if ($classId > 0) {
                $studentsQuery->where('my_class_id', $classId);
            }

            $students = $studentsQuery->get();

            return response()->json(
                $students->map(function ($student) {
                    return [
                        'id'             => $student->id,
                        'name'           => $student->user->name ?? 'N/A',
                        'adm_no'         => $student->adm_no ?? '',
                        'user_id_hashed' => Qs::hash($student->user_id),
                    ];
                })->values()
            );
        } catch (Throwable $exception) {
            return response()->json([
                'error'   => true,
                'message' => 'Server error while fetching students.',
            ], 500);
        }
    }

    
}
