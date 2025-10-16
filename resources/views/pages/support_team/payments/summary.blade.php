@extends('layouts.master')
@section('page_title', 'Fees Summary')

@section('content')

<div class="row">

    {{-- Total Paid This Month --}}
    {{-- <div class="col-lg-4 col-md-6 mb-4">
        <div class="card shadow-sm border-left-primary h-100">
            <div class="card-body text-center">
                <div class="mb-2">
                    <i class="icon-check icon-4x text-primary"></i>
                </div>
                <h5 class="card-title text-uppercase text-muted">Total Monthly Fees</h5>

                <h3 class="font-weight-bold">{{ number_format($total_fee, 2) }}</h3>
            </div>
        </div>
    </div> --}}

    {{-- Total Payments --}}
    <div class="col-lg-4 col-md-6 mb-4">
        <div class="card shadow-sm border-left-warning h-100">
            <div class="card-body text-center">
                <div class="mb-2">
                    <i class="icon-cash3 icon-4x text-warning"></i>
                </div>
                <h5 class="card-title text-uppercase text-muted">Total Paid To This Month</h5>
                <h3 class="font-weight-bold">{{ number_format($current_month_paid, 2) }}</h3>
            </div>
        </div>
    </div>



    {{-- Pending Amount This Month --}}
    {{-- <div class="col-lg-4 col-md-6 mb-4">
        <div class="card shadow-sm border-left-danger h-100">
            <div class="card-body text-center">
                <div class="mb-2">
                    <i class="icon-hour-glass2 icon-4x text-danger"></i>
                </div>
                <h5 class="card-title text-uppercase text-muted">Pending Amount</h5>
                <h3 class="font-weight-bold">{{ number_format(($students_count * $yearly_amount_sum)
                    -$current_month_paid, 2) }}</h3>
            </div>
        </div>
    </div> --}}

</div>

{{-- Filter by Class --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('payments.summary') }}">
            <div class="form-row align-items-center">
                <div class="col-sm-4 my-1">
                    <select name="class_id" class="form-control">
                        <option value="">-- Select Class --</option>
                        @foreach(App\Models\MyClass::orderBy('name')->get() as $c)
                        <option value="{{ $c->id }}" {{ request('class_id')==$c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto my-1">
                    <button type="submit" class="btn btn-primary">Filter</button>
                    <a href="{{ route('payments.summary') }}" class="btn btn-secondary">Reset</a>
                </div>
            </div>
        </form>
    </div>
</div>
@if(isset($students) && $students->count() > 0)
<div class="card shadow-sm mt-4">
    <div class="card-header bg-light">
        <h6 class="mb-0">Students in Selected Class</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-striped mb-0">
            <thead class="thead-dark">
                <tr>
                    <th>S / ID</th>
                    <th>Student Name</th>
                    <th>Paid To This Month</th>
                </tr>
            </thead>
            <tbody>
                @foreach($students as $index => $student)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $student->name }}</td>
                    {{-- <td>{{ number_format($student->fee_demand, 2) }}</td> --}}
                    <td>{{ number_format($student->paid_this_month, 2) }}</td>

                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@elseif(request('class_id'))
<div class="alert alert-warning mt-4">
    No students found for this class.
</div>
@endif
@endsection