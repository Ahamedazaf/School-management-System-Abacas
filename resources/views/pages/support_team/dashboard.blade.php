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
    </div> --}}

    <div class="col-sm-6 col-xl-3">
        <div class="card card-body bg-warning-400 has-bg-image">
            <div class="media">
                <div class="media-body">
                    <h3 class="mb-0">{{ number_format($total_amount, 2) }}</h3>
                    <span class="text-uppercase font-size-xs font-weight-bold">Anual Total Payments</span>
                </div>

                <div class="ml-3 align-self-center">
                    <i class="icon-cash3 icon-3x opacity-75"></i>
                </div>
            </div>
        </div>
    </div>


    <div class="col-sm-6 col-xl-3">
        <div class="card card-body bg-primary-400 has-bg-image">
            <div class="media">
                <div class="media-body">
                    <h3 class="mb-0">{{ number_format($total_paid_till_now, 2) }}</h3>
                    <span class="text-uppercase font-size-xs font-weight-bold">Total Paid (Jan - {{
                        \Carbon\Carbon::now()->format('F') }})</span>
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
                    <h3 class="mb-0">{{ number_format($pending_amount, 2) }}</h3>
                    <span class="text-uppercase font-size-xs font-weight-bold">Pending Amount (Annual)</span>
                </div>
                <div class="ml-3 align-self-center">
                    <i class="icon-cross2 icon-3x opacity-75"></i>
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

</div>

@endsection