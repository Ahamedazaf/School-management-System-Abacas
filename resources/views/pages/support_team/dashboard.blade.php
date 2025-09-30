@extends('layouts.master')
@section('page_title', 'My Dashboard')
@section('content')

@if (Qs::userIsTeamSA())
<div class="row">
    <div class="col-sm-6 col-xl-3">
        <div class="card card-body bg-blue-400 has-bg-image">
            <div class="media">
                <div class="media-body">
                    <h3 class="mb-0">{{ $users->where('user_type', 'student')->count() }}</h3>
                    <span class="text-uppercase font-size-xs font-weight-bold">Total Students</span>
                </div>

                <div class="ml-3 align-self-center">
                    <i class="icon-users4 icon-3x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- <div class="col-sm-6 col-xl-3">
        <div class="card card-body bg-danger-400 has-bg-image">
            <div class="media">
                <div class="media-body">
                    <h3 class="mb-0">{{ $users->where('user_type', 'teacher')->count() }}</h3>
                    <span class="text-uppercase font-size-xs">Total Teachers</span>
                </div>

                <div class="ml-3 align-self-center">
                    <i class="icon-users2 icon-3x opacity-75"></i>
                </div>
            </div>
        </div>
    </div> --}}

    {{-- <div class="col-sm-6 col-xl-3">
        <div class="card card-body bg-success-400 has-bg-image">
            <div class="media">
                <div class="mr-3 align-self-center">
                    <i class="icon-pointer icon-3x opacity-75"></i>
                </div>

                <div class="media-body text-right">
                    <h3 class="mb-0">{{ $users->where('user_type', 'admin')->count() }}</h3>
                    <span class="text-uppercase font-size-xs">Total Administrators</span>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="col-sm-6 col-xl-3">
        <div class="card card-body bg-indigo-400 has-bg-image">
            <div class="media">
                <div class="mr-3 align-self-center">
                    <i class="icon-user icon-3x opacity-75"></i>
                </div>

                <div class="media-body text-right">
                    <h3 class="mb-0">{{ $users->where('user_type', 'parent')->count() }}</h3>
                    <span class="text-uppercase font-size-xs">Total Parents</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card card-body bg-warning-400 has-bg-image">
            <div class="media">
                <div class="media-body">
                    <h3 class="mb-0">{{ number_format($total_fee, 2) }}</h3>
                    <span class="text-uppercase font-size-xs font-weight-bold">Total Payments</span>
                </div>

                <div class="ml-3 align-self-center">
                    <i class="icon-cash3 icon-3x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>







    {{-- <div class="col-sm-6 col-xl-3">
        <div class="card card-body bg-blue-400 has-bg-image">
            <div class="media">
                <div class="media-body">
                    <h3 class="mb-0">{{ $students_count }}</h3>
                    <span class="text-uppercase font-size-xs font-weight-bold">Total Students</span>
                </div>
                <div class="ml-3 align-self-center">
                    <i class="icon-users4 icon-3x opacity-75"></i>
                </div>
            </div>
        </div>
    </div> --}}

    {{-- <div class="col-sm-6 col-xl-3">
        <div class="card card-body bg-success-400 has-bg-image">
            <div class="media">
                <div class="media-body">
                    <h3 class="mb-0">{{ number_format($total_fee) }}</h3>
                    <span class="text-uppercase font-size-xs font-weight-bold">Total Fee Demand</span>
                </div>
                <div class="ml-3 align-self-center">
                    <i class="icon-cash3 icon-3x opacity-75"></i>
                </div>
            </div>
        </div>
    </div> --}}

    <div class="col-sm-6 col-xl-3">
        <div class="card card-body bg-primary-400 has-bg-image">
            <div class="media">
                <div class="media-body">
                    <h3 class="mb-0">{{ number_format($current_month_paid) }}</h3>
                    <span class="text-uppercase font-size-xs font-weight-bold">Total Paid This Month</span>
                </div>
                <div class="ml-3 align-self-center">
                    <i class="icon-check icon-3x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-sm-6 col-xl-3">
        <div class="card card-body bg-danger-400 has-bg-image">
            <div class="media">
                <div class="media-body">
                    <h3 class="mb-0">{{ number_format($pending_amount) }}</h3>
                    <span class="text-uppercase font-size-xs font-weight-bold">Pending Amount This Month</span>
                </div>
                <div class="ml-3 align-self-center">
                    <i class="icon-cross2 icon-3x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>



</div>
@endif

<!-- CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.dataTables.min.css">

<!-- JS -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>



{{-- Students Table --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
        <h5 class="mb-0 fw-semibold text-dark">
            <i class="icon-users4 text-primary me-2"></i> Students Fee Status
        </h5>
        <span class="badge bg-light text-muted">Last Updated: {{ now()->format('d M, Y') }}</span>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="students-table" class="table table-hover table-bordered align-middle mb-0">
                <thead class="bg-light text-dark">
                    <tr>
                        <th>ID</th>
                        <th>Student Name</th>
                        <th>Fee Demand</th>
                        <th>Paid (This Month)</th>
                        <th>Pending</th>
                        <th>Month Payment Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($students as $student)
                    <tr>
                        <td class="fw-semibold">{{ $student->id }}</td>
                        <td class="fw-medium">{{ $student->name }}</td>
                        <td>Rs. {{ number_format($student->fee_demand) }}</td>
                        <td class="text-success fw-semibold">
                            Rs. {{ number_format($student->monthly_paid) }}
                        </td>
                        <td>
                            @if($student->pending > 0)
                            <span class="badge bg-danger px-3 py-2">
                                Rs. {{ number_format($student->pending) }}
                            </span>
                            @else
                            <span class="badge bg-success px-3 py-2">
                                No Due
                            </span>
                            @endif
                        </td>
                        <td>
                            @foreach ($student->months_status as $monthNum => $status)
                            @php $monthName = \Carbon\Carbon::create()->month($monthNum)->format('M'); @endphp
                            @if($status === 'Paid')
                            <span class="badge bg-success mb-1">
                                <i class="bi bi-check-circle me-1"></i> {{ $monthName }}
                            </span>
                            @else
                            <span class="badge bg-danger mb-1">
                                <i class="bi bi-x-circle me-1"></i> {{ $monthName }}
                            </span>
                            @endif
                            @endforeach
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>


<style>
    #students-table th {
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: .5px;
    }

    #students-table tbody tr:hover {
        background-color: #f8f9fa;
        transition: 0.2s ease-in-out;
    }

    .badge {
        border-radius: 30px;
        font-weight: 600;
        font-size: 0.75rem;
    }
</style>

{{-- *Name Search Javascripht Function --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        $('#students-table').DataTable({
            responsive: true,
            pageLength: 10,
            lengthChange: false,
            ordering: true,
            pagingType: 'simple_numbers',
            language: {
                search: "_INPUT_",
                searchPlaceholder: "Search by name or ID..."
            }
        });
    });
</script>


{{-- Events Calendar Begins --}}
<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title">School Events Calendar</h5>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        <div class="fullcalendar-basic"></div>
    </div>
</div>
{{-- Events Calendar Ends --}}
@endsection