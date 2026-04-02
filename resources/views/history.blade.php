<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>APARA | Payment History</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
@include('layouts.sidebar')
@include('layouts.header')
@php
    $paymentCollection = $payments->getCollection();
    $paidCount = $paymentCollection->where('status', 'paid')->count();
    $pendingCount = $paymentCollection->where('status', 'pending')->count();
    $failedCount = $paymentCollection->where('status', 'failed')->count();
@endphp
<div class="main with-sidebar">
<div class="page-shell">
  <div class="page-header">
    <div class="page-header-content">
      <h4 class="page-heading">Payment History</h4>
      <p class="page-subtitle">Payment history now follows the same shared layout without changing any payment data.</p>
    </div>
  </div>
  <div class="card filter-card mb-3 payment-history-filter-card">
    <div class="row g-4 align-items-stretch">
      <div class="col-lg-7">
        <form method="GET" action="{{ route('history') }}" class="row g-3 h-100">
          <div class="col-md-6">
            <label class="form-label">Payment Ref</label>
            <input type="text" class="form-control" name="payment_ref" value="{{ request('payment_ref') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">From</label>
            <input type="date" class="form-control" name="from_date" value="{{ request('from_date') }}">
          </div>
          <div class="col-md-3">
            <label class="form-label">To</label>
            <input type="date" class="form-control" name="to_date" value="{{ request('to_date') }}">
          </div>
          <div class="col-md-6">
            <label class="form-label">Status</label>
            <select class="form-select" name="status">
              <option value="">All</option>
              <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
              <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
              <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
            </select>
          </div>
          <div class="col-md-6 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Search</button>
          </div>
        </form>
      </div>
      <div class="col-lg-5">
        <div class="payment-history-summary">
          <div class="payment-history-summary-head">
            <h5>Current Summary</h5>
            <span>Based on the records shown below</span>
          </div>
          <div class="payment-history-summary-grid">
            <div class="payment-history-summary-item">
              <small>Total Records</small>
              <strong>{{ $paymentCollection->count() }}</strong>
            </div>
            <div class="payment-history-summary-item">
              <small>Paid</small>
              <strong>{{ $paidCount }}</strong>
            </div>
            <div class="payment-history-summary-item">
              <small>Pending</small>
              <strong>{{ $pendingCount }}</strong>
            </div>
            <div class="payment-history-summary-item">
              <small>Failed</small>
              <strong>{{ $failedCount }}</strong>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="data-card">
      <div class="table-title-row">
        <h5>Payment History Records</h5>
        <span class="table-meta">Existing payment details stay exactly the same.</span>
      </div>
  <div class="table-responsive">
      <table class="table table-bordered table-hover">
        <thead>
          <tr>
            <th>Payment Ref</th>
            <th>Date</th>
            <th>Type</th>
            <th>Count</th>
            <th>Total Amount (LKR)</th>
            <th>Status</th>
            <th>Notes</th>
          </tr>
        </thead>
        <tbody>
          @forelse($payments as $payment)
            <tr>
              <td>{{ $payment->payment_ref }}</td>
              <td>{{ \Carbon\Carbon::parse($payment->payment_date)->format('Y-m-d') }}</td>
              <td>{{ $payment->payment_type }}</td>
              <td>{{ $payment->record_count }}</td>
              <td>{{ number_format((float) $payment->total_amount, 2) }}</td>
              <td>{{ ucfirst($payment->status) }}</td>
              <td>{{ $payment->notes ?? '-' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center">No payment history records found.</td>
            </tr>
          @endforelse
        </tbody>
      </table>
  </div>
  </div>
  <div class="mt-3">
    {{ $payments->links() }}
  </div>
</div>
</div>
</body>
</html>
