@extends('layouts.app')

@section('title', 'Support Tickets - APARA System')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-1">Support Tickets</h4>
        <p class="text-muted mb-0">Track incidents, service requests, and SLA risks</p>
    </div>
    <button class="btn btn-primary">Create Ticket</button>
</div>

<div class="row g-3 mb-4">
    <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Open</div><h3 class="mb-0">{{ $ticketSummary['open'] }}</h3></div></div>
    <div class="col-md-3"><div class="card p-3"><div class="text-muted small">In Progress</div><h3 class="mb-0">{{ $ticketSummary['in_progress'] }}</h3></div></div>
    <div class="col-md-3"><div class="card p-3"><div class="text-muted small">Resolved</div><h3 class="mb-0">{{ $ticketSummary['resolved'] }}</h3></div></div>
    <div class="col-md-3"><div class="card p-3"><div class="text-muted small">SLA Risk</div><h3 class="mb-0">{{ $ticketSummary['sla_risk'] }}</h3></div></div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-4 h-100">
            <h5 class="mb-3">Current Ticket Queue</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Ticket ID</th>
                            <th>Subject</th>
                            <th>Priority</th>
                            <th>Owner</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                            <tr>
                                <td>{{ $ticket['id'] }}</td>
                                <td>{{ $ticket['subject'] }}</td>
                                <td>{{ $ticket['priority'] }}</td>
                                <td>{{ $ticket['owner'] }}</td>
                                <td>{{ $ticket['status'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card p-4 h-100">
            <h5 class="mb-3">Escalation Notes</h5>
            <div class="border rounded p-3 bg-light mb-3">
                High-priority tickets should be acknowledged within 30 minutes and assigned immediately.
            </div>
            <div class="border rounded p-3 bg-light">
                Payment-related incidents should include invoice number, payment reference, and timestamp for faster resolution.
            </div>
        </div>
    </div>
</div>
@endsection
