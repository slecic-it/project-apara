@extends('layouts.app')

@section('title', 'Blacklist - APARA System')

@section('content')
<div class="page-shell">
    <div class="page-header">
        <div class="page-header-content">
            <h3 class="page-heading">Blacklist</h3>
            <p class="page-subtitle">View-only blacklist and watchlist records available to bank-side users.</p>
        </div>
    </div>

    <div class="card data-card">
        <div class="table-title-row">
            <h5>Restricted Records</h5>
            <span class="table-meta">These entries are displayed for reference only.</span>
        </div>

        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Reference</th>
                        <th>Bank</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Last Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($blacklistEntries as $entry)
                        <tr>
                            <td>{{ $entry['reference'] }}</td>
                            <td>{{ $entry['bank_name'] }}</td>
                            <td>{{ $entry['subject'] }}</td>
                            <td>{{ $entry['status'] }}</td>
                            <td>{{ $entry['updated_at'] }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">No blacklist records available.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
