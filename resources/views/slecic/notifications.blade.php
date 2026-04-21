@extends('layouts.app')

@section('title', 'SLECIC Notifications - APARA System')

@section('content')
<div class="page-shell">
    <section class="card data-card bank-dashboard-panel mb-4">
        <div class="table-title-row">
            <div>
                <h5>{{ $notifications['title'] ?? 'SLECIC Notifications' }}</h5>
                <span class="table-meta">{{ $notifications['subtitle'] ?? 'Marketing notifications for SLECIC.' }}</span>
            </div>
            <span class="table-count-pill">Unread: {{ $notifications['count'] ?? 0 }} / {{ $notifications['total_count'] ?? 0 }}</span>
        </div>

        <div class="notification-section-block">
            <div class="notification-section-head">
                <h6>Unread Notifications</h6>
                <span>{{ count($notifications['unread_items'] ?? []) }}</span>
            </div>

            <div class="app-notification-page-list">
                @forelse(($notifications['unread_items'] ?? []) as $notification)
                    <div class="app-notification-item app-notification-page-item app-notification-{{ $notification['tone'] ?? 'info' }}">
                        <span class="app-notification-item-icon">
                            <i class="bi {{ $notification['icon'] ?? 'bi-bell' }}"></i>
                        </span>
                        <div class="app-notification-item-copy">
                            <div class="app-notification-item-topline">
                                <strong>{{ $notification['title'] ?? 'Notification' }}</strong>
                                <small>{{ $notification['time'] ?? 'Just now' }}</small>
                            </div>
                            <span class="app-notification-stage">{{ $notification['stage'] ?? 'Workflow' }}</span>
                            <p>{{ $notification['message'] ?? '' }}</p>
                            <div class="app-notification-page-meta">
                                <span><strong>Proposal:</strong> {{ $notification['proposal'] ?? '-' }}</span>
                                <span><strong>Customer:</strong> {{ $notification['customer'] ?? '-' }}</span>
                            </div>
                            <div class="notification-action-row">
                                <a href="{{ $notification['url'] ?? '#' }}" class="btn btn-sm btn-common">Open</a>
                                <form action="{{ route('notifications.read', $notification['id']) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Mark as Read</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="app-notification-empty">
                        <i class="bi bi-bell-slash"></i>
                        <p>No unread marketing notifications are available right now.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="notification-section-block mt-4">
            <div class="notification-section-head">
                <h6>Read Notifications</h6>
                <span>{{ count($notifications['read_items'] ?? []) }}</span>
            </div>

            <div class="app-notification-page-list">
                @forelse(($notifications['read_items'] ?? []) as $notification)
                    <div class="app-notification-item app-notification-page-item app-notification-read app-notification-{{ $notification['tone'] ?? 'info' }}">
                        <span class="app-notification-item-icon">
                            <i class="bi {{ $notification['icon'] ?? 'bi-bell' }}"></i>
                        </span>
                        <div class="app-notification-item-copy">
                            <div class="app-notification-item-topline">
                                <strong>{{ $notification['title'] ?? 'Notification' }}</strong>
                                <small>{{ $notification['time'] ?? 'Just now' }}</small>
                            </div>
                            <span class="app-notification-stage">{{ $notification['stage'] ?? 'Workflow' }}</span>
                            <p>{{ $notification['message'] ?? '' }}</p>
                            <div class="app-notification-page-meta">
                                <span><strong>Proposal:</strong> {{ $notification['proposal'] ?? '-' }}</span>
                                <span><strong>Customer:</strong> {{ $notification['customer'] ?? '-' }}</span>
                            </div>
                            <div class="notification-action-row">
                                <a href="{{ $notification['url'] ?? '#' }}" class="btn btn-sm btn-common">Open</a>
                                <form action="{{ route('notifications.unread', $notification['id']) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-secondary">Mark as Unread</button>
                                </form>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="app-notification-empty">
                        <i class="bi bi-check2-all"></i>
                        <p>No read notifications yet.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
</div>
@endsection
