@extends('layouts.app')

@section('title', 'Yearly Report - APARA System')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Yearly Report</h4>
        <p class="text-muted mb-0">Month-by-month view for {{ $summary['year'] }}</p>
    </div>
    <span class="badge bg-success-subtle text-success px-3 py-2">{{ $summary['year'] }}</span>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card p-3 h-100">
            <div class="text-muted small">Applications Total</div>
            <h3 class="mb-0">{{ $summary['applications_total'] }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 h-100">
            <div class="text-muted small">Premium Generated</div>
            <h3 class="mb-0">LKR {{ number_format((float) $summary['yearly_premium_total'], 2) }}</h3>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 h-100">
            <div class="text-muted small">Payments Collected</div>
            <h3 class="mb-0">LKR {{ number_format((float) $summary['yearly_payment_total'], 2) }}</h3>
        </div>
    </div>
</div>

<div class="card p-4 mb-4">
    <h5 class="mb-3">Application Overview</h5>
    <div class="row g-3">
        @foreach($summary['applications'] as $status => $count)
            <div class="col-md-3">
                <div class="border rounded p-3 bg-light h-100">
                    <div class="text-muted text-capitalize small">{{ $status }}</div>
                    <div class="fs-4 fw-semibold">{{ $count }}</div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<div class="card p-4">
    <h5 class="mb-3">Monthly Breakdown</h5>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Month</th>
                    <th>Invoices</th>
                    <th>Premium Total</th>
                    <th>Payments</th>
                    <th>Payment Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($months as $month)
                    <tr>
                        <td>{{ $month['label'] }}</td>
                        <td>{{ $month['invoice_count'] }}</td>
                        <td>LKR {{ number_format((float) $month['premium_total'], 2) }}</td>
                        <td>{{ $month['payment_count'] }}</td>
                        <td>LKR {{ number_format((float) $month['payment_total'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
