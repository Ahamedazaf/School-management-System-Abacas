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

                    <!-- Monthly amount row (auto-calculated) -->
                    <div class="form-group row">
                        <label for="monthly_amount" class="col-lg-3 col-form-label font-weight-semibold">
                            Monthly Amount (LKR)
                        </label>
                        <div class="col-lg-9">
                            <input class="form-control" readonly name="monthly_amount" id="monthly_amount"
                                type="number">
                        </div>
                    </div>

                    <div class="form-group row">
                        <label for="description"
                            class="col-lg-3 col-form-label font-weight-semibold">Fee Description</label>
                        <div class="col-lg-9">
                            <input class="form-control" value="{{ old('description') }}" name="description"
                                id="description" type="text">
                        </div>
                    </div>

                    <!-- SECTION 2: FEE BREAKDOWN -->
                    <div class="mb-4">
                        <h5 class="text-lg font-semibold text-gray-700 mb-3">Additional Items Fee</h5>
                        <div id="feeItemsContainer" class="space-y-3">
                            <!-- Initial Fee Item -->
                            {{-- <div class="row g-3 align-items-center fee-item bg-gray-50 p-3 rounded-lg border"> --}}
                                {{-- <div class="col-sm-5">
                                    <label for="itemName_1" class="form-label visually-hidden">Item Name</label>
                                    <input type="text" class="form-control rounded-md p-2" id="itemName_1"
                                        placeholder="Eg: Dress code" required>
                                </div> --}}
                                {{-- <div class="col-sm-5">
                                    <label for="itemAmount_1" class="form-label visually-hidden">Amount</label>
                                    <div class="input-group">
                                        <span class="input-group-text rounded-l-md">LKR</span>
                                        <input type="number" step="0.01" class="form-control rounded-r-md p-2"
                                            id="itemAmount_1" placeholder="0.00" min="0" required
                                            oninput="calculateTotal()">
                                    </div>
                                </div> --}}
                                {{-- <div class="col-sm-2 d-grid">
                                    <button type="button" class="btn btn-outline-danger" onclick="removeFeeItem(this)">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                            fill="currentColor" viewBox="0 0 16 16">
                                            <path
                                                d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H2.5zm3 3a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 4zm3 .5a.5.5 0 0 0-1 0v7a.5.5 0 0 0 1 0v-7z" />
                                        </svg>
                                    </button>
                                </div> --}}
                                {{-- </div> --}}
                        </div>
                        <!-- Add Item Button -->
                        <div class="mt-2">
                            <button type="button" class="btn btn-outline-primary w-100 py-2 rounded"
                                onclick="addFeeItem()">
                                {{-- <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                    class="inline-block align-text-top me-1" viewBox="0 0 16 16">
                                    <path
                                        d="M8 4a.5.5 0 0 1 .5.5v3h3a.5.5 0 0 1 0 1h-3v3a.5.5 0 0 1-1 0v-3h-3a.5.5 0 0 1 0-1h3v-3A.5.5 0 0 1 8 4z" />
                                </svg> --}}
                                Add Fee Item
                            </button>
                        </div>
                    </div>

                    <!-- SECTION 3: TOTAL & SUBMIT -->
                    <div class="d-flex justify-content-between align-items-center bg-gray-100 p-4 rounded-xl mt-3">
                        <div class="text-xl font-bold text-gray-800">
                            <span class="text-lg font-semibold text-dark">Total Additional Amount: <span
                                    id="totalAdditionalAmount" class="text-primary">LKR 0.00</span></span><br>
                            <span class="text-lg font-semibold text-dark">Total Yearly Amount: <span
                                    id="totalYearlyAmount" class="text-primary">LKR 0.00</span></span><br><br>
                            <h5 class="text-lg font-semibold text-dark">Total Amount: <span id="totalAmount"
                                    class="text-primary">LKR 0.00</span></h5>
                        </div>
                        <input type="hidden" name="amount" id="amount_hidden" value="0">
                        <input type="hidden" name="additional_items" id="additional_items">
                        <input type="hidden" name="total_amount" id="total_amount_hidden" value="0">
                        <input type="hidden" name="additional_amount" id="additional_amount" value="0">
                        <button type="submit" class="btn btn-primary btn-lg">Submit Payment <i
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
 
        // Function to add a new fee item breakdown row
        function addFeeItem() {
            feeItemCounter++;
            const container = document.getElementById('feeItemsContainer');
            const newRow = document.createElement('div');
            newRow.classList.add('row', 'g-3', 'align-items-center', 'fee-item', 'bg-gray-50', 'p-3', 'rounded-lg', 'border');
            newRow.innerHTML = `
                <div class="col-sm-5">
                    <label for="itemName_${feeItemCounter}" class="form-label visually-hidden">Item Name</label>
                    <input type="text" class="form-control rounded-md p-2" id="itemName_${feeItemCounter}" placeholder="Miscellaneous Fee" >
                </div>
                <div class="col-sm-5">
                    <label for="itemAmount_${feeItemCounter}" class="form-label visually-hidden">Amount</label>
                    <div class="input-group">
                        <span class="input-group-text rounded-l-md">LKR</span>
                        <input type="number" step="0.01" class="form-control rounded-r-md p-2" id="itemAmount_${feeItemCounter}" placeholder="0.00" min="0"  oninput="calculateTotal()">
                    </div>
                </div>
                <div class="col-sm-2 d-grid">
                    <button type="button" class="btn btn-outline-danger position-center" onclick="removeFeeItem(this)">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                            <path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H2.5zm3 3a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 4zm3 .5a.5.5 0 0 0-1 0v7a.5.5 0 0 0 1 0v-7z"/>
                        </svg>
                    </button>
                </div>
            `;
            container.appendChild(newRow);
            // Focus on the new item name field
            document.getElementById(`itemName_${feeItemCounter}`).focus();
        }
 
        // Function to remove a fee item breakdown row
        function removeFeeItem(button) {
            // Check if it's the last item (should always have at least one)
            const allItems = document.querySelectorAll('.fee-item');
            if (allItems.length > 1) {
                const row = button.closest('.fee-item');
                row.remove();
                calculateTotal();
            } else {
                // Instead of alert, display a message or prevent deletion
                console.warn("Cannot remove the last fee item.");
            }
        }
 
        // Update monthly when yearly changes
document.getElementById('amount').addEventListener('input', function () {
    let yearly = parseFloat(this.value);
    let monthlyField = document.getElementById('monthly_amount');
 
    if (!isNaN(yearly) && yearly > 0) {
        let monthly = (yearly / 12).toFixed(2);
        monthlyField.value = monthly;
    } else {
        monthlyField.value = "";
    }
 
    calculateYearlyTotal(); // <-- new call
    calculateFinalTotal();  // <-- update final total
});
 
let feeItemCounter = 1;
 
// Function to calculate total yearly amount
function calculateYearlyTotal() {
    let yearly = parseFloat(document.getElementById('amount').value) || 0;
    document.getElementById('totalYearlyAmount').textContent = `LKR ${yearly.toFixed(2)}`;
}
 
// Function to calculate total additional items
function calculateTotal() {
    let total = 0;
    const amountInputs = document.querySelectorAll('input[id^="itemAmount_"]');
 
    amountInputs.forEach(input => {
        const value = parseFloat(input.value) || 0;
        total += value;
    });
 
    document.getElementById('totalAdditionalAmount').textContent = `LKR ${total.toFixed(2)}`;
 
    calculateFinalTotal(); // <-- update final total
}
 
// Function to calculate grand total (yearly + additional)
function calculateFinalTotal() {
    let yearly = parseFloat(document.getElementById('amount').value) || 0;
    let additional = 0;
    let items = [];
 
    // collect all fee items
    document.querySelectorAll('.fee-item').forEach((item) => {
        let name = item.querySelector(`[id^="itemName_"]`).value || "";
        let amount = parseFloat(item.querySelector(`[id^="itemAmount_"]`).value) || 0;
        items.push({ name: name, amount: amount });
        additional += amount;
    });
 
    let finalTotal = yearly + additional;
 
    // update UI
    document.getElementById('totalAdditionalAmount').textContent = `LKR ${additional.toFixed(2)}`;
    document.getElementById('totalAmount').textContent = `LKR ${finalTotal.toFixed(2)}`;
 
    // update hidden inputs (so backend can save them)
    document.getElementById('amount_hidden').value = yearly.toFixed(2);
    document.getElementById('additional_items').value = JSON.stringify(items);
    document.getElementById('total_amount_hidden').value = finalTotal.toFixed(2);
    document.getElementById('additional_amount').value = additional.toFixed(2);
}
 
 
// Initial calculation on load
window.onload = function() {
    calculateYearlyTotal();
    calculateTotal();
    calculateFinalTotal();
};
 
</script>

@endsection