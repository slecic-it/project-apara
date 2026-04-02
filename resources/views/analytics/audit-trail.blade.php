@extends('layouts.app')

@section('title', 'Audit Trail - APARA System')

@section('content')
<div class="mb-4">
    <h4 class="mb-1">Audit Trail</h4>
    <p class="text-muted mb-0">Recent invoice and payment actions for internal review</p>
</div>

<div class="row g-4">
    <div class="col-lg-7">
        <div class="card p-4 h-100">
            <h5 class="mb-3">Invoice Activity</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Reference</th>
                            <th>Customer</th>
                            <th>Status</th>
                            <th>Approval</th>
                            <th>Created</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($invoiceActivities as $activity)
                            <tr>
                                <td>{{ $activity->reference }}</td>
                                <td>{{ $activity->subject }}</td>
                                <td>{{ ucfirst($activity->status) }}</td>
                                <td>{{ ucfirst($activity->approval_status) }}</td>
                                <td>{{ $activity->created_at ? \Illuminate\Support\Carbon::parse($activity->created_at)->format('Y-m-d H:i') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No invoice audit data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-5">
        <div class="card p-4 h-100">
            <h5 class="mb-3">Payment Activity</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Reference</th>
                            <th>Type</th>
                            <th>Status</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($paymentActivities as $activity)
                            <tr>
                                <td>{{ $activity->reference }}</td>
                                <td>{{ $activity->subject }}</td>
                                <td>{{ ucfirst($activity->status) }}</td>
                                <td>{{ $activity->payment_date ? \Illuminate\Support\Carbon::parse($activity->payment_date)->format('Y-m-d') : '-' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="text-center text-muted">No payment audit data available.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
