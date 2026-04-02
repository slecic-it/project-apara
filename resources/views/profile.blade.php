@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="page-shell">
    <div class="page-header">
        <div class="page-header-content">
            <h3 class="page-heading">Profile</h3>
            <p class="page-subtitle">Visible details for the person currently logged in.</p>
        </div>
    </div>

    <div class="profile-layout">
        <section class="card data-card profile-summary-card">
            <img
                src="{{ $profile['profile_photo'] }}"
                alt="{{ $profile['name'] }} profile photo"
                class="profile-photo"
            >
            <h4>{{ $profile['name'] ?? 'System User' }}</h4>
            <p>{{ $profile['designation'] ?? 'System User' }}</p>

            <div class="profile-badge-list">
                <span class="profile-badge">{{ $profile['department'] ?? '-' }}</span>
                <span class="profile-badge profile-badge-active">{{ $profile['status'] ?? 'Active' }}</span>
            </div>
        </section>

        <section class="card data-card settings-panel">
            <div class="table-title-row">
                <h5>Logged-In User Details</h5>
                <span class="table-meta">Only the current user’s visible account details are shown here.</span>
            </div>

            <div class="profile-detail-grid">
                <div class="profile-detail-item">
                    <small>Full Name</small>
                    <strong>{{ $profile['name'] ?? '-' }}</strong>
                </div>
                <div class="profile-detail-item">
                    <small>Email Address</small>
                    <strong>{{ $profile['email'] ?? '-' }}</strong>
                </div>
                <div class="profile-detail-item">
                    <small>Mobile Number</small>
                    <strong>{{ $profile['mobile_no'] ?? '-' }}</strong>
                </div>
                <div class="profile-detail-item">
                    <small>Employee Number</small>
                    <strong>{{ $profile['employee_no'] ?? '-' }}</strong>
                </div>
                <div class="profile-detail-item">
                    <small>Designation / Role</small>
                    <strong>{{ $profile['designation'] ?? '-' }}</strong>
                </div>
                <div class="profile-detail-item">
                    <small>Department / Branch</small>
                    <strong>{{ $profile['department'] ?? '-' }}</strong>
                </div>
                <div class="profile-detail-item">
                    <small>Account Status</small>
                    <strong>{{ $profile['status'] ?? '-' }}</strong>
                </div>
                <div class="profile-detail-item">
                    <small>Member Since</small>
                    <strong>{{ $profile['member_since'] ?? '-' }}</strong>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
