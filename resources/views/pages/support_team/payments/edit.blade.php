@extends('layouts.master')
@section('page_title', 'Edit Payment')
@section('content')

<div class="card">
    <div class="card-header bg-transparent border-bottom">
        <h4 class="mb-0">Edit Payment</h4>
    </div>

    <div class="card-body">
        @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert" id="successAlert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        @endif


        <form id="editPaymentForm" method="POST" action="{{ route('payments.update', $payment->id) }}">
            @csrf
            @method('PUT')

            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Payment Title</label>
                    <input type="text" name="title" class="form-control" value="{{ $payment->title }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Monthly Amount (LKR)</label>
                    <input type="number" name="amount" class="form-control" value="{{ $payment->amount }}" step="0.01"
                        required>
                </div>
            </div>

            {{-- description --}}
            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label fw-bold">Fee Description</label>
                    <input type="text" name="description" class="form-control" value="{{ $payment->description }}">
                </div>
            </div>

            {{-- <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Class</label>
                    <select name="my_class_id" class="form-select">
                        <option value="">-- Select Class --</option>
                        @foreach ($my_classes as $c)
                        <option value="{{ $c->id }}" {{ $payment->my_class_id == $c->id ? 'selected' : '' }}>
                            {{ $c->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold">Type</label>
                    <select name="type" class="form-select" required>
                        <option value="monthly" {{ $payment->type == 'monthly' ? 'selected' : '' }}>Monthly</option>
                        <option value="yearly" {{ $payment->type == 'yearly' ? 'selected' : '' }}>Yearly</option>
                        <option value="one_time" {{ $payment->type == 'one_time' ? 'selected' : '' }}>One Time</option>
                    </select>
                </div>
            </div> --}}

            <!-- Additional Items Section -->
            <div class="mt-4">
                <h5 class="fw-bold">Additional Items</h5>
                <table class="table table-bordered align-middle mt-2" id="additionalItemsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Item Name</th>
                            <th>Amount (LKR)</th>
                            <th style="width: 50px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $items = $payment->additional_items
                        ? json_decode($payment->additional_items, true)
                        : [];
                        @endphp
                        @forelse($items as $item)
                        <tr>
                            <td><input type="text" class="form-control item-name" value="{{ $item['name'] }}">
                            </td>
                            <td><input type="number" class="form-control item-amount" value="{{ $item['amount'] }}"
                                    step="0.01"></td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-danger remove-item"><i
                                        class="bi bi-trash"></i>x</button>
                            </td>
                        </tr>
                        @empty
                        <tr class="no-items">
                            <td colspan="3" class="text-center text-muted">No additional items added</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table><br>

                <button type="button" class="btn btn-sm btn-primary" id="addItemBtn">
                    <i class="bi bi-plus"></i> Add Item
                </button>
            </div>

            <input type="hidden" name="additional_items" id="additional_items">

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-success">Update Payment</button>
                <a href="{{ route('payments.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
            // Hide success message after 3 seconds
            const alert = document.getElementById('successAlert');
            if (alert) {
                setTimeout(() => {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                }, 3000);
            }
 
            const addItemBtn = document.getElementById('addItemBtn');
            const tableBody = document.querySelector('#additionalItemsTable tbody');
            const hiddenInput = document.getElementById('additional_items');
            const form = document.getElementById('editPaymentForm');
 
            // Add new item row
            addItemBtn.addEventListener('click', function() {
                const noItemsRow = tableBody.querySelector('.no-items');
                if (noItemsRow) noItemsRow.remove();
 
                const row = document.createElement('tr');
                row.innerHTML = `
            <td><input type="text" class="form-control item-name" placeholder="Item name" required></td>
            <td><input type="number" class="form-control item-amount" placeholder="0.00" step="0.01" required></td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-danger remove-item"><i class="bi bi-x"></i>x</button>
            </td>
        `;
                tableBody.appendChild(row);
            });
 
            // Remove item row
            tableBody.addEventListener('click', function(e) {
                if (e.target.closest('.remove-item')) {
                    e.target.closest('tr').remove();
                    if (tableBody.children.length === 0) {
                        tableBody.innerHTML =
                            `<tr class="no-items"><td colspan="3" class="text-center text-muted">No additional items added</td></tr>`;
                    }
                }
            });
 
            // Handle form submit
            form.addEventListener('submit', function(e) {
                const items = [];
                tableBody.querySelectorAll('tr').forEach(row => {
                    const nameInput = row.querySelector('.item-name');
                    const amountInput = row.querySelector('.item-amount');
                    if (nameInput && amountInput && nameInput.value.trim() !== '') {
                        items.push({
                            name: nameInput.value.trim(),
                            amount: parseFloat(amountInput.value) || 0
                        });
                    }
                });
                hiddenInput.value = JSON.stringify(items);
            });
        });
</script>
@endsection