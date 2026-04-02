@extends('layouts.app')

@section('title', 'Performance Report - APARA System')

@section('content')
<div class="mb-4">
    <h4 class="mb-1">Performance Report</h4>
    <p class="text-muted mb-0">Key delivery metrics across operations, finance, and support</p>
</div>

<div class="row g-3 mb-4">
    @foreach($metrics as $metric)
        <div class="col-md-6 col-xl-3">
            <div class="card p-3 h-100">
                <div class="text-muted small">{{ $metric['label'] }}</div>
                <h3 class="mt-2 mb-1">{{ $metric['value'] }}</h3>
                <div class="text-muted small">{{ $metric['hint'] }}</div>
            </div>
        </div>
    @endforeach
</div>

<div class="row g-4">
    <div class="col-lg-5">
        <div class="card p-4 h-100">
            <h5 class="mb-3">Application Distribution</h5>
            @foreach($applicationCounts as $status => $count)
                <div class="d-flex justify-content-between border-bottom py-2">
                    <span class="text-capitalize">{{ $status }}</span>
                    <strong>{{ $count }}</strong>
                </div>
            @endforeach
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card p-4 h-100">
            <h5 class="mb-3">Team Performance Snapshot</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Team</th>
                            <th>Completed</th>
                            <th>Backlog</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($teams as $team)
                            <tr>
                                <td>{{ $team['team'] }}</td>
                                <td>{{ $team['completed'] }}</td>
                                <td>{{ $team['backlog'] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
