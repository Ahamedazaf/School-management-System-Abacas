@extends('layouts.master')
@section('page_title', 'Manage Payments')
@section('content')

<div class="card">
    <div class="card-header header-elements-inline">
        <h6 class="card-title font-weight-bold">Manage Payment Records for {{ $sr->user->name }}</h6>
        {!! Qs::getPanelOptions() !!}
    </div>

    <div class="card-body">
        <ul class="nav nav-tabs nav-tabs-highlight">
            <li class="nav-item"><a href="#all-uc" class="nav-link active" data-toggle="tab">Incomplete Payments</a>
            </li>
            <li class="nav-item"><a href="#all-cl" class="nav-link" data-toggle="tab">Completed Payments</a></li>
            <li class="nav-item"><a href="#additional-fine" class="nav-link" data-toggle="tab">Additional Fine
                    Payments</a></li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="all-uc">
                <table class="table datatable-button-html5-columns table-responsive">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Pay Ref</th>
                            <th>Yearly Amount</th>
                            <th>Paid</th>
                            <th>Months</th>
                            <th>Pay Now</th>
                            <th>Receipt No</th>
                            <th>Year</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($uncleared as $uc)
                        @php
                        $monthsList =
                        ['January','February','March','April','May','June','July','August','September','October','November','December'];
                        $paidMonths = $uc->paid_months ? (is_array($uc->paid_months) ? $uc->paid_months :
                        json_decode($uc->paid_months, true)) : [];
                        $hash = Qs::hash($uc->id);
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $uc->payment->title }}</td>
                            <td>{{ $uc->payment->ref_no }}</td>
                            <td class="font-weight-bold">{{ number_format($uc->payment->amount, 2) }}</td>
                            <td class="text-blue font-weight-bold">{{ number_format($uc->amt_paid ?: 0, 2) }}</td>
                            <td style="min-width:220px;">
                                <select id="months-select-{{ $hash }}" class="form-control months-select" multiple
                                    data-year-amount="{{ $uc->payment->amount }}" data-hash="{{ $hash }}">
                                    @foreach($monthsList as $m)
                                    @php $isPaid = in_array($m, $paidMonths ?? [], true); @endphp
                                    <option value="{{ $m }}" @if($isPaid) disabled style="background:#eee;color:#666;"
                                        @endif>
                                        {{ $m }} @if($isPaid) (Paid) @endif
                                    </option>
                                    @endforeach
                                </select>
                            </td>
                            <td style="min-width:210px;">
                                <form id="form-{{ $hash }}" method="post" class="ajax-pay"
                                    action="{{ route('payments.pay_now', Qs::hash($uc->id)) }}">
                                    @csrf
                                    <div class="d-flex flex-column">
                                        <div class="mb-2">
                                            <span class="badge badge-primary"
                                                id="pay-amount-display-{{ $hash }}">0.00</span>
                                            <small class="text-muted" id="pay-months-count-{{ $hash }}">(0
                                                months)</small>
                                        </div>
                                        <div id="hidden-months-{{ $hash }}"></div>
                                        <button class="btn btn-danger" type="submit" id="pay-btn-{{ $hash }}" disabled>
                                            Pay <i class="icon-paperplane ml-2"></i>
                                        </button>
                                    </div>
                                </form>
                            </td>
                            <td>{{ $uc->ref_no }}</td>
                            <td>{{ $uc->year }}</td>
                            <td class="text-center">
                                <div class="list-icons">
                                    <div class="dropdown">
                                        <a href="#" class="list-icons-item" data-toggle="dropdown"><i
                                                class="icon-menu9"></i></a>
                                        <div class="dropdown-menu dropdown-menu-left">
                                            <a id="{{ $hash }}" onclick="confirmReset(this.id)" href="#"
                                                class="dropdown-item">
                                                <i class="icon-reset"></i> Reset Payment
                                            </a>
                                            <form method="post" id="item-reset-{{ $hash }}"
                                                action="{{ route('payments.reset_record', $uc->id) }}" class="hidden">
                                                @csrf @method('delete')
                                            </form>
                                            <a target="_blank" href="{{ route('payments.receipts', $uc->id) }}"
                                                class="dropdown-item">
                                                <i class="icon-printer"></i> Print Receipt
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="all-cl">
                <table class="table datatable-button-html5-columns table-responsive">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Title</th>
                            <th>Pay Ref</th>
                            <th>Amount</th>
                            <th>Receipt No</th>
                            <th>Year</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cleared as $cl)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $cl->payment->title }}</td>
                            <td>{{ $cl->payment->ref_no }}</td>
                            <td class="font-weight-bold">{{ number_format($cl->payment->amount, 2) }}</td>
                            <td>{{ $cl->ref_no }}</td>
                            <td>{{ $cl->year }}</td>
                            <td class="text-center">
                                <div class="list-icons">
                                    <div class="dropdown">
                                        <a href="#" class="list-icons-item" data-toggle="dropdown"><i
                                                class="icon-menu9"></i></a>
                                        <div class="dropdown-menu dropdown-menu-left">
                                            <a id="{{ Qs::hash($cl->id) }}" onclick="confirmReset(this.id)" href="#"
                                                class="dropdown-item">
                                                <i class="icon-reset"></i> Reset Payment
                                            </a>
                                            <form method="post" id="item-reset-{{ Qs::hash($cl->id) }}"
                                                action="{{ route('payments.reset_record', $cl->id) }}" class="hidden">
                                                @csrf @method('delete')
                                            </form>
                                            <a target="_blank" href="{{ route('payments.receipts', $cl->id) }}"
                                                class="dropdown-item">
                                                <i class="icon-printer"></i> Print Receipt
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Fine --}}
            <div class="tab-pane fade" id="additional-fine">
                <form id="fineForm" action="{{ route('fines.store') }}" method="POST">
                    @csrf

                    <input type="hidden" name="user_id" value="{{ $sr->user->id ?? '' }}">

                    <div class="row g-5">
                        <div class="col-lg-6 border-end" style="border-color: #e5e7eb;">
                            <h6 class="fw-bold text-secondary mb-3" style="font-size: 1.1rem;">
                                <i class="icon-list2 mr-1"></i> Fine Breakdown
                            </h6>

                            <div id="fineItemsContainer" class="mb-4">
                                <div class="row g-3 align-items-end fine-item mb-3 p-3 rounded-3"
                                    style="background: #f8f9fa; border: 1px solid #e5e7eb;">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted">Item Name</label>
                                        <input type="text" name="itemName[]" class="form-control rounded-3"
                                            placeholder="e.g., What Lost !" required>
                                    </div>

                                    <div class="col-md-4">
                                        <label class="form-label text-muted">Amount (LKR)</label>
                                        <input type="number" step="0.01" class="form-control rounded-3"
                                            name="itemAmount[]" placeholder="Amount" min="0" required
                                            oninput="calculateTotal()">
                                    </div>

                                    <div class="col-md-2 d-flex align-items-end">
                                        <button type="button" class="btn btn-outline-danger w-100 rounded-3"
                                            onclick="removeFineItem(this)">
                                            <i class="icon-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid mb-5">
                                <button type="button" class="btn btn-outline-secondary py-2 fw-semibold rounded-3"
                                    onclick="addFineItem()">
                                    <i class="icon-plus-circle2"></i> Add Another Item
                                </button>
                            </div>

                            <div class="p-4 rounded-3 border text-center"
                                style="background: #fdfdfd; border-color: #e5e7eb;">
                                <div class="fs-5 fw-bold mb-0">
                                    Total Fine: <span id="totalAmount" class="text-success">0.00</span>
                                </div>
                            </div>

                            <div class="mt-4 text-end">
                                <button type="submit" class="btn btn-success btn-lg px-5 rounded-3">
                                    <i class="icon-paperplane mr-2"></i> Generate Invoice
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>





            <div id="toast" style="
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: #28a745;
    color: #fff;
    padding: 14px 24px;
    border-radius: 8px;
    font-weight: 600;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.4s ease, transform 0.4s ease;
    z-index: 9999;
">Payment Successful</div>

            <script>
                (function () {
    function showToast(message) {
        const toast = document.getElementById('toast');
        toast.textContent = message;
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';
        }, 2000);
    }

    function showError(message) {
        alert(message || 'Payment failed. Please try again.');
    }

    function updateRow(hash) {
        var select = document.getElementById('months-select-' + hash);
        var yearlyAmount = parseFloat(select.getAttribute('data-year-amount')) || 0;
        var monthly = yearlyAmount / 12;
        var selected = Array.from(select.selectedOptions)
            .filter(o => !o.disabled)
            .map(o => o.value);

        var amount = monthly * selected.length;

        document.getElementById('pay-amount-display-' + hash).textContent = amount.toFixed(2);
        document.getElementById('pay-months-count-' + hash).textContent =
            '(' + selected.length + ' month' + (selected.length !== 1 ? 's' : '') + ')';

        var hiddenWrap = document.getElementById('hidden-months-' + hash);
        hiddenWrap.innerHTML = '';
        selected.forEach(function (m) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'months[]';
            input.value = m;
            hiddenWrap.appendChild(input);
        });

        document.getElementById('pay-btn-' + hash).disabled = selected.length === 0;
    }

    document.querySelectorAll('.months-select').forEach(function (sel) {
        var hash = sel.getAttribute('data-hash');
        sel.addEventListener('change', function () { updateRow(hash); });
        updateRow(hash);
    });

    document.querySelectorAll('.ajax-pay').forEach(function(form) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const hash = form.id.replace('form-', '');
            updateRow(hash);

            fetch(form.action, {
                method: 'POST',
                body: new FormData(form),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(async (res) => {
                let data = {};
                try { data = await res.json(); } catch (_) {}

                if (!res.ok) {
                    if (res.status === 419) return showError('Session expired (419). Please refresh and try again.');
                    if (res.status === 401 || res.status === 403) return showError('Not authorized. Please sign in again.');
                    if (res.status === 422) {
                        const msg = (data && data.message) || 'Validation failed. Select at least one month.';
                        return showError(msg);
                    }
                    return showError((data && (data.msg || data.message)) || 'Server error. Please try again.');
                }

                if (data.ok) {
                    showToast('Payment Successful!');
                    setTimeout(() => {
                        document.body.style.transition = 'opacity 0.5s ease';
                        document.body.style.opacity = '0';
                        setTimeout(() => window.location.reload(), 500);
                    }, 1200);
                } else {
                    showError(data.msg || 'Payment failed. Please try again.');
                }
            })
            .catch(() => showError('Network error. Please try again.'));
        });
    });
})();
            </script>

            <script>
                document.addEventListener("DOMContentLoaded", function () {
    const token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    window.calculateTotal = function () {
        let total = 0;
        document.querySelectorAll('input[name="itemAmount[]"]').forEach(input => {
            total += parseFloat(input.value) || 0;
        });
        document.getElementById('totalAmount').textContent = total.toFixed(2);
    };

    window.addFineItem = function () {
        const container = document.getElementById('fineItemsContainer');
        const row = document.createElement('div');
        row.className = "row g-3 align-items-end fine-item mb-3 p-3 rounded-3";
        row.style.background = "#f8f9fa";
        row.style.border = "1px solid #e5e7eb";
        row.innerHTML = `
            <div class="col-md-6">
                <label class="form-label text-muted">Item Name</label>
                <input type="text" class="form-control rounded-3" name="itemName[]" placeholder="e.g., What Lost !" required>
            </div>
            <div class="col-md-4">
                <label class="form-label text-muted">Amount (LKR)</label>
                <input type="number" step="0.01" class="form-control rounded-3" name="itemAmount[]" placeholder="(LKR) 0.00" min="0" required oninput="calculateTotal()">
            </div>
            <div class="col-md-2">
                <button type="button" class="btn btn-outline-danger w-100 mt-4 rounded-3" onclick="removeFineItem(this)">
                    <i class="icon-trash"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
    };

    window.removeFineItem = function (button) {
        const rows = document.querySelectorAll('.fine-item');
        if (rows.length > 1) {
            button.closest('.fine-item').remove();
            calculateTotal();
        } else {
            alert("At least one breakdown item is required.");
        }
    };

    document.getElementById('fineForm').addEventListener('submit', async function (e) {
        e.preventDefault();
        const form = this;
        const total = document.getElementById('totalAmount').textContent;
        const formData = new FormData(form);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                }
            });

            const data = await response.json();
            if (response.ok && data.success) {
                showFineToast(` ${data.message} Total Fine: ${total}`);
                form.reset();
                document.getElementById('fineItemsContainer').innerHTML = '';
                addFineItem();
                calculateTotal();
            } else {
                alert('Failed to save invoice. ' + (data.message || 'Unknown error'));
            }
        } catch (error) {
            console.error(error);
            alert('Network error. Please try again.');
        }
    });

    function showFineToast(message) {
        let toast = document.getElementById('toast');
        if (!toast) {
            toast = document.createElement('div');
            toast.id = 'toast';
            document.body.appendChild(toast);
        }
        toast.textContent = message;
        toast.style.opacity = '1';
        toast.style.transform = 'translateY(0)';
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';
        }, 3000);
    }

    calculateTotal();
});
            </script>


            @endsection