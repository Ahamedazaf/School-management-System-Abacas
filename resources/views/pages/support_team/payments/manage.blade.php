@extends('layouts.master')
@section('page_title', 'Student Payments')

@section('content')
<div class="card">
    <div class="card-header header-elements-inline">
        <h5 class="card-title"><i class="icon-cash2 mr-2"></i> Student Payments</h5>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6 offset-md-3">
                <div class="form-group">
                    <label for="class_selector" class="fw-bold">Class:</label>
                    <select id="class_selector" class="form-control select">
                        <option value="0">All Classes</option>
                        @foreach($my_classes as $class)
                        <option value="{{ $class->id }}" {{ (isset($my_class_id) && $my_class_id==$class->id) ?
                            'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6 offset-md-3">
                <div class="form-group">
                    <label for="student_search" class="fw-bold">Search Name:</label>
                    <input type="text" id="student_search" class="form-control" placeholder="Enter student name">
                </div>
            </div>
        </div>

        <div id="student_list" class="mt-4 text-center text-muted">
            Loading students...
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(function () {
    const $studentList = $('#student_list');
    const $classSelect = $('#class_selector');
    const $searchInput = $('#student_search');
    const STUDENT_URL = "{{ route('payments.fetch_students') }}";

    function displayStudents(students) {
        if (!Array.isArray(students) || students.length === 0) {
            $studentList.html('<div class="alert alert-warning">No students found.</div>');
            return;
        }

        let tableHtml = `
            <table class="table table-bordered table-striped">
                <thead class="fw-bold fs-5">
                    <tr>
                        <th>S/N</th>
                        <th>Name</th>
                        <th>Payments</th>
                    </tr>
                </thead>
                <tbody class="fw-bold fs-6">
        `;

        students.forEach((student, index) => {
            const invoiceUrl = "{{ url('payments/invoice') }}/" + student.user_id_hashed;
            tableHtml += `
                <tr>
                    <td>${index + 1}</td>
                    <td>${student.name}</td>
                    <td><a href="${invoiceUrl}" class="btn btn-danger btn-sm fw-bold">Manage Payments</a></td>
                </tr>
            `;
        });

        tableHtml += `</tbody></table>`;
        $studentList.html(tableHtml);
    }

    function loadStudents(classId = 0) {
        $studentList.html('Loading students...');

        $.ajax({
            url: `${STUDENT_URL}?class_id=${classId}`,
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                displayStudents(data);
            },
            error: function (xhr) {
                let message = 'Error fetching students.';
                if (xhr.status === 404) message += ' (404 Not Found)';
                if (xhr.status === 500) message += ' (500 Server Error)';
                $studentList.html(`<div class="alert alert-danger">${message}</div>`);
            }
        });
    }

    loadStudents(0);

    $classSelect.on('change', function () {
        const classId = $(this).val() || 0;
        loadStudents(classId);
    });

    $searchInput.on('keyup', function() {
        const keyword = $(this).val().toLowerCase();
        $('#student_list table tbody tr').each(function () {
            const name = $(this).find('td:nth-child(2)').text().toLowerCase();
            $(this).toggle(name.includes(keyword));
        });
    });
});
</script>
@endsection