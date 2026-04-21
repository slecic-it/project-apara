<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>APARA | Fee Due</title>
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
            <h4 class="page-heading">Guarantee Invoice Due Applications</h4>
            <p class="page-subtitle">This finance page now matches the shared design while keeping all existing invoice details unchanged.</p>
        </div>
    </div>
    <form method="GET" action="{{ route($feeDueRouteName ?? 'fee_due') }}" class="card filter-card row g-3 mb-3">
        <div class="col-md-4">
            <label class="form-label">Search By ID Number</label>
            <input type="text" class="form-control" name="id_no" value="{{ request('id_no') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">From</label>
            <input type="date" class="form-control" name="from_date" value="{{ request('from_date') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label">To</label>
            <input type="date" class="form-control" name="to_date" value="{{ request('to_date') }}">
        </div>
        <div class="col-md-2 d-flex align-items-end">
            <button type="submit" class="btn btn-primary w-100">Search</button>
        </div>
    </form>
    <div class="data-card">
        <div class="table-title-row">
            <h5>Fee Due Records</h5>
            <span class="table-meta">Outstanding invoice details stay exactly the same.</span>
        </div>
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead class="table-dark">
                <tr>
                    <th>Application No</th>
                    <th>Proposal No</th>
                    <th>Invoice No</th>
                    <th>Comments</th>
                    <th>ID No</th>
                    <th>Passport No</th>
                    <th>Invoiced Date</th>
                    <th>Approval</th>
                    <th>Premium</th>
                </tr>
            </thead>
            <tbody>
                @forelse($invoices as $invoice)
                    <tr>
                        <td>{{ $invoice->application_no ?? '-' }}</td>
                        <td>{{ $invoice->proposal_no ?? '-' }}</td>
                        <td>{{ $invoice->invoice_no }}</td>
                        <td>{{ $invoice->comments ?? '-' }}</td>
                        <td>{{ $invoice->id_no }}</td>
                        <td>{{ $invoice->passport_no ?? '-' }}</td>
                        <td>{{ $invoice->invoiced_at ? \Carbon\Carbon::parse($invoice->invoiced_at)->format('Y-m-d') : '-' }}</td>
                        <td>{{ ucfirst($invoice->approval_status) }}</td>
                        <td>LKR {{ number_format((float) $invoice->premium_amount, 2) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center">No fee-due invoices found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    </div>
</div>
</div>
</body>
</html>
