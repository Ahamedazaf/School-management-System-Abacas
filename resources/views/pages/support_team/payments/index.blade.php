@extends('layouts.master')
@section('page_title', 'Manage Payments')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title"><i class="icon-cash2 mr-2"></i> Select year</h5>
        {!! Qs::getPanelOptions() !!}
    </div>

    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
        <i class="icon-checkmark-circle"></i> {{ session('success') }}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>

    <script>
        // Auto-hide alert after 3 seconds
        setTimeout(() => {
            const alert = document.getElementById('successAlert');
            if (alert) {
                $(alert).fadeOut('slow');
            }
        }, 3000);
    </script>
    @endif


    <div class="card-body">
        <form method="post" action="{{ route('payments.select_year') }}">
            @csrf
            <div class="row">

                <div class="col-md-6 offset-md-3">
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group">
                                <label for="year" class="col-form-label font-weight-bold">Select Year <span
                                        class="text-danger">*</span></label>
                                <select data-placeholder="Select Year" required id="year" name="year"
                                    class="form-control select">
                                    <option {{ ($selected && $year==Qs::getCurrentSession()) ? 'selected' : '' }}
                                        value="{{ Qs::getCurrentSession() }}">{{ Qs::getCurrentSession() }}</option>
                                    @foreach ($years as $yr)
                                    <option {{ ($selected && $year==$yr->year) ? 'selected' : '' }} value="{{ $yr->year
                                        }}">{{ $yr->year }}</option>

                                    {{-- <option value="{{ $yr->year }}" {{ $years==$yr->year ? 'selected' : '' }}>
                                        {{ $yr->year }}
                                    </option> --}}
                                    @endforeach
                                </select>


                            </div>
                        </div>

                        <div class="col-md-2 mt-4">
                            <div class="text-right mt-1">
                                <button type="submit" class="btn btn-primary">Submit <i
                                        class="icon-paperplane ml-2"></i></button>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </form>
    </div>
</div>

@if ($selected)
<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Manage Payments for {{ $year }} Session</h6>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-highlight">
            <li class="nav-item"><a href="#all-payments" class="nav-link active" data-toggle="tab">All Classes</a>
            </li>
            <li class="nav-item dropdown">
                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown">Class Payments</a>
                <div class="dropdown-menu dropdown-menu-right">
                    @foreach ($my_classes as $mc)
                    <a href="#pc-{{ $mc->id }}" class="dropdown-item" data-toggle="tab">{{ $mc->name }}</a>
                    @endforeach
                </div>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="all-payments">
                <table class="table datatable-button-html5-columns">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Yearly Amount</th>
                            <th>Additional Items</th>
                            <th>Additional Amount</th>
                            <th>Ref_No</th>
                            <th>Class</th>
                            {{-- <th>Method</th> --}}
                            <th>Fee Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments as $p)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $p->title }}</td>
                            <td>{{ $p->amount }}</td>
                            <td>
                                @php
                                $items = $p->additional_items ? json_decode($p->additional_items, true) : [];
                                @endphp
                                @if (!empty($items))
                                <ul class="list-unstyled mb-0">
                                    @foreach ($items as $item)
                                    <li>{{ $item['name'] }}: LKR {{ number_format($item['amount'], 2) }}</li>
                                    @endforeach
                                </ul>
                                @else
                                N/A
                                @endif
                            <td>{{ $p->additional_amount }}</td>
                            <td>{{ $p->ref_no }}</td>
                            <td>{{ $p->my_class_id ? $p->my_class->name : '' }}</td>
                            {{-- <td>{{ ucwords($p->method) }}</td> --}}
                            <td>{{ $p->description }}</td>
                            <td class="text-center">
                                <div class="list-icons">
                                    <div class="dropdown">
                                        <a href="#" class="list-icons-item" data-toggle="dropdown">
                                            <i class="icon-menu9"></i>
                                        </a>

                                        <div class="dropdown-menu dropdown-menu-left">
                                            {{-- Edit --}}
                                            <a href="{{ route('payments.edit', $p->id) }}" class="dropdown-item"><i
                                                    class="icon-pencil"></i> Edit</a>
                                            {{-- Delete --}}
                                            <a id="{{ $p->id }}" onclick="confirmDelete(this.id)" href="#"
                                                class="dropdown-item"><i class="icon-trash"></i>
                                                Delete</a>
                                            <form method="post" id="item-delete-{{ $p->id }}"
                                                action="{{ route('payments.destroy', $p->id) }}" class="hidden">@csrf
                                                @method('delete')</form>

                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @foreach ($my_classes as $mc)
            <div class="tab-pane fade" id="pc-{{ $mc->id }}">
                <table class="table datatable-button-html5-columns">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>YearlyAmount</th>
                            <th>Additional Items</th>
                            <th>Additional Amount</th>
                            <th>Ref_No</th>
                            <th>Class</th>
                            {{-- <th>Method</th> --}}
                            <th>Fee Description</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($payments->where('my_class_id', $mc->id) as $p)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $p->title }}</td>
                            <td>{{ $p->amount }}</td>
                            <td>
                                @php
                                $items = $p->additional_items ? json_decode($p->additional_items, true) : [];
                                @endphp
                                @if (!empty($items))
                                <ul class="list-unstyled mb-0">
                                    @foreach ($items as $item)
                                    <li>{{ $item['name'] }}: LKR {{ number_format($item['amount'], 2) }}</li>
                                    @endforeach
                                </ul>
                                @else
                                N/A
                                @endif
                            <td>{{ $p->additional_amount }}</td>
                            <td>{{ $p->ref_no }}</td>
                            <td>{{ $p->my_class_id ? $p->my_class->name : '' }}</td>
                            {{-- <td>{{ ucwords($p->method) }}</td> --}}
                            <td>{{ $p->description }}</td>
                            <td class="text-center">
                                <div class="list-icons">
                                    <div class="dropdown">
                                        <a href="#" class="list-icons-item" data-toggle="dropdown">
                                            <i class="icon-menu9"></i>
                                        </a>

                                        <div class="dropdown-menu dropdown-menu-left">
                                            {{-- Edit --}}
                                            <a href="{{ route('payments.edit', $p->id) }}" class="dropdown-item"><i
                                                    class="icon-pencil"></i> Edit</a>
                                            {{-- Delete --}}
                                            <a id="{{ $p->id }}" onclick="confirmDelete(this.id)" href="#"
                                                class="dropdown-item"><i class="icon-trash"></i> Delete</a>
                                            <form method="post" id="item-delete-{{ $p->id }}"
                                                action="{{ route('payments.destroy', $p->id) }}" class="hidden">@csrf
                                                @method('delete')</form>

                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

            </div>
            @endforeach
        </div>
    </div>
</div>
@endif

@endsection