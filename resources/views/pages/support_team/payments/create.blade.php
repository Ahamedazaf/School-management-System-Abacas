@extends('layouts.master')
@section('page_title', 'Create Payment')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title">Create Payment</h6>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <form class="ajax-store" method="post" action="{{ route('payments.store') }}">
                    @csrf
                    <div class="form-group row">
                        <label class="col-lg-3 col-form-label font-weight-semibold">Title <span
                                class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input name="title" value="{{ old('title') }}" required type="text" class="form-control"
                                placeholder="Eg. School Fees">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="my_class_id" class="col-lg-3 col-form-label font-weight-semibold">Class </label>
                        <div class="col-lg-9">
                            <select class="form-control select-search" name="my_class_id" id="my_class_id">
                                <option value="">All Classes</option>
                                @foreach($my_classes as $c)
                                <option {{ old('my_class_id')==$c->id ? 'selected' : '' }} value="{{ $c->id }}">{{
                                    $c->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- <div class="form-group row">
                        <label for="student_id" class="col-lg-3 col-form-label font-weight-semibold">Student
                            Details</label>
                        <div class="col-lg-9">
                            <select class="form-control select-search" name="student_id" id="student_id">
                                <option value="">All Students</option>
                                @foreach($students as $student)
                                <option value="{{ $student->user_id }}" data-class-id="{{ $student->my_class_id }}" {{
                                    old('student_id')==$student->user_id ? 'selected' : '' }}>
                                    {{ $student->user->name }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div> --}}

                    {{-- <div class="form-group row">
                        <label for="method" class="col-lg-3 col-form-label font-weight-semibold">Payment Method</label>
                        <div class="col-lg-9">
                            <select class="form-control select" name="method" id="method">
                                <option selected value="Cash">Cash</option>
                                <option disabled value="Online">Online</option>
                            </select>
                        </div>
                    </div> --}}

                    <div class="form-group row">
                        <label for="amount" class="col-lg-3 col-form-label font-weight-semibold">Amount (<del
                                style="text-decoration-style: double">LKR</del>) <span
                                class="text-danger">*</span></label>
                        <div class="col-lg-9">
                            <input class="form-control" value="{{ old('amount') }}" required name="amount" id="amount"
                                type="number">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="description"
                            class="col-lg-3 col-form-label font-weight-semibold">Description</label>
                        <div class="col-lg-9">
                            <input class="form-control" value="{{ old('description') }}" name="description"
                                id="description" type="text">
                        </div>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">Submit form <i
                                class="icon-paperplane ml-2"></i></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
    var allStudents = $('#student_id option:not(:first)').clone();
    
    $('#my_class_id').on('change', function() {
        var selectedClassId = $(this).val();
        var studentSelect = $('#student_id');
        
        studentSelect.find('option:not(:first)').remove();
        
        if (selectedClassId === '') {
            studentSelect.append(allStudents.clone());
        } else {
            allStudents.each(function() {
                if ($(this).data('class-id') == selectedClassId) {
                    studentSelect.append($(this).clone());
                }
            });
        }
    });
});
</script>
@endsection
