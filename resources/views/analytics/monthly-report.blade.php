@extends('layouts.app')

@section('title', 'Monthly Report - APARA System')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Monthly Report</h4>
        <p class="text-muted mb-0">Operational and finance snapshot for {{ $summary['periodLabel'] }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route($monthlyReportPdfRouteName ?? 'monthly-report.pdf') }}" class="btn btn-outline-primary btn-sm">Download PDF Summary</a>
        <span class="badge bg-primary-subtle text-primary px-3 py-2">{{ $summary['periodLabel'] }}</span>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card p-3 h-100">
            <div class="text-muted small">Applications in Pipeline</div>
            <h3 class="mb-0">{{ $summary['applications_total'] }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 h-100">
            <div class="text-muted small">Invoices Created</div>
            <h3 class="mb-0">{{ $summary['invoices_created'] }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 h-100">
            <div class="text-muted small">Invoices Paid</div>
            <h3 class="mb-0">{{ $summary['invoices_paid'] }}</h3>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card p-3 h-100">
            <div class="text-muted small">Payments Collected</div>
            <h3 class="mb-0">LKR {{ number_format((float) $summary['payments_total'], 2) }}</h3>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card p-4 h-100">
            <h5 class="mb-3">Application Status Mix</h5>
            <div class="d-flex flex-column gap-3">
                @foreach($summary['applications'] as $label => $value)
                    <div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-capitalize">{{ $label }}</span>
                            <strong>{{ $value }}</strong>
                        </div>
                        <div class="progress" style="height: 10px;">
                            <div class="progress-bar" role="progressbar" style="width: {{ $summary['applications_total'] ? ($value / $summary['applications_total']) * 100 : 0 }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card p-4 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="mb-0">Recent Invoice Activity</h5>
                <span class="text-muted small">Premium total: LKR {{ number_format((float) $summary['premium_total'], 2) }}</span>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Invoice No</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Premium</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentInvoices as $invoice)
                            <tr>
                                <td>{{ $invoice->invoice_no }}</td>
                                <td>{{ $invoice->customer_name }}</td>
                                <td><span class="badge bg-secondary">{{ ucfirst($invoice->status) }}</span></td>
                                <td>LKR {{ number_format((float) $invoice->premium_amount, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No invoice activity recorded for this month.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
