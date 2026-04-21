<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>APARA | Payments</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>
@include('layouts.sidebar')
@include('layouts.header')
<div class="main with-sidebar">
<div class="page-shell">
  <div class="page-header">
    <div class="page-header-content">
      <h4 class="page-heading">Payments</h4>
      <p class="page-subtitle">Track processed payment batches, monitor totals, and filter finance records from one place.</p>
    </div>
  </div>

  <div class="settings-overview-grid mb-3">
    <div class="settings-stat-card">
      <span class="settings-stat-label">Total Records</span>
      <strong>{{ $paymentSummary['total_records'] ?? 0 }}</strong>
    </div>
    <div class="settings-stat-card">
      <span class="settings-stat-label">Paid</span>
      <strong>{{ $paymentSummary['paid_records'] ?? 0 }}</strong>
    </div>
    <div class="settings-stat-card">
      <span class="settings-stat-label">Pending</span>
      <strong>{{ $paymentSummary['pending_records'] ?? 0 }}</strong>
    </div>
    <div class="settings-stat-card">
      <span class="settings-stat-label">Total Amount</span>
      <strong>LKR {{ number_format((float) ($paymentSummary['total_amount'] ?? 0), 2) }}</strong>
    </div>
  </div>

  <div class="card filter-card mb-3 payment-history-filter-card">
    <form method="GET" action="{{ route($paymentsRouteName ?? 'payments') }}" class="row g-3">
      <div class="col-md-3">
        <label class="form-label">Payment Ref</label>
        <input type="text" class="form-control" name="payment_ref" value="{{ request('payment_ref') }}">
      </div>
      <div class="col-md-3">
        <label class="form-label">Payment Type</label>
        <input type="text" class="form-control" name="payment_type" value="{{ request('payment_type') }}">
      </div>
      <div class="col-md-2">
        <label class="form-label">Status</label>
        <select class="form-select" name="status">
          <option value="">All</option>
          <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
          <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
          <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Failed</option>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">From</label>
        <input type="date" class="form-control" name="from_date" value="{{ request('from_date') }}">
      </div>
      <div class="col-md-2">
        <label class="form-label">To</label>
        <input type="date" class="form-control" name="to_date" value="{{ request('to_date') }}">
      </div>
      <div class="col-12 d-flex justify-content-end">
        <button type="submit" class="btn btn-primary">Search</button>
      </div>
    </form>
  </div>

  <div class="data-card">
    <div class="table-title-row">
      <div>
        <h5>Payment Records</h5>
        <span class="table-meta">Each row represents a payment batch recorded by the finance team.</span>
      </div>
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
              <td>
                <span class="badge
                  @if ($payment->status === 'paid') bg-success
                  @elseif ($payment->status === 'pending') bg-warning text-dark
                  @else bg-danger
                  @endif">
                  {{ ucfirst($payment->status) }}
                </span>
              </td>
              <td>{{ $payment->notes ?? '-' }}</td>
            </tr>
          @empty
            <tr>
              <td colspan="7" class="text-center">No payment records found.</td>
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
