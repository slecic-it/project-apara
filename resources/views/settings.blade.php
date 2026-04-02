@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="page-shell">
    <div class="page-header">
        <div class="page-header-content">
            <h3 class="page-heading">Settings</h3>
            <p class="page-subtitle">Manage workspace preferences, system access options, and notification behavior from one place.</p>
        </div>
        <div class="page-actions">
            <button class="btn btn-outline-primary">Reset Changes</button>
            <button class="btn btn-primary">Save Settings</button>
        </div>
    </div>

    <div class="settings-overview-grid mb-4">
        <div class="settings-stat-card">
            <span class="settings-stat-label">Environment</span>
            <strong>{{ strtoupper($settingsSummary['environment'] ?? 'production') }}</strong>
        </div>
        <div class="settings-stat-card">
            <span class="settings-stat-label">Timezone</span>
            <strong>{{ $settingsSummary['timezone'] ?? 'UTC' }}</strong>
        </div>
        <div class="settings-stat-card">
            <span class="settings-stat-label">Locale</span>
            <strong>{{ strtoupper($settingsSummary['locale'] ?? 'en') }}</strong>
        </div>
        <div class="settings-stat-card">
            <span class="settings-stat-label">Notifications</span>
            <strong>{{ $settingsSummary['notifications'] ?? 'Enabled' }}</strong>
        </div>
    </div>

    <div class="settings-grid">
        <section class="card data-card settings-panel">
            <div class="table-title-row">
                <h5>General Preferences</h5>
                <span class="table-meta">Core dashboard defaults and display options.</span>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="card-label">System Name</label>
                    <input type="text" class="form-control" value="SLECIC Operations Portal">
                </div>
                <div class="col-md-6">
                    <label class="card-label">Default Language</label>
                    <select class="form-select">
                        <option selected>English</option>
                        <option>Sinhala</option>
                        <option>Tamil</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="card-label">Timezone</label>
                    <select class="form-select">
                        <option selected>Asia/Colombo</option>
                        <option>UTC</option>
                        <option>Asia/Dubai</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="card-label">Date Format</label>
                    <select class="form-select">
                        <option selected>DD MMM YYYY</option>
                        <option>MM/DD/YYYY</option>
                        <option>YYYY-MM-DD</option>
                    </select>
                </div>
            </div>
        </section>

        <section class="card data-card settings-panel">
            <div class="table-title-row">
                <h5>Notifications</h5>
                <span class="table-meta">Decide which updates should appear in the header and inbox.</span>
            </div>

            <div class="settings-toggle-list">
                <label class="settings-toggle-item">
                    <div>
                        <strong>Application Updates</strong>
                        <span>Show alerts when pending items move to accepted or approved.</span>
                    </div>
                    <input class="form-check-input" type="checkbox" checked>
                </label>

                <label class="settings-toggle-item">
                    <div>
                        <strong>Finance Reminders</strong>
                        <span>Send notices for due fees, receipts, and overdue invoices.</span>
                    </div>
                    <input class="form-check-input" type="checkbox" checked>
                </label>

                <label class="settings-toggle-item">
                    <div>
                        <strong>Support Escalations</strong>
                        <span>Highlight urgent support tickets in the workspace header.</span>
                    </div>
                    <input class="form-check-input" type="checkbox">
                </label>
            </div>
        </section>

        <section class="card data-card settings-panel">
            <div class="table-title-row">
                <h5>Security</h5>
                <span class="table-meta">Recommended access and session controls for internal users.</span>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="card-label">Session Timeout</label>
                    <select class="form-select">
                        <option>15 Minutes</option>
                        <option selected>30 Minutes</option>
                        <option>60 Minutes</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="card-label">Password Rotation</label>
                    <select class="form-select">
                        <option>30 Days</option>
                        <option selected>60 Days</option>
                        <option>90 Days</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="card-label">Login Alerts</label>
                    <div class="settings-inline-options">
                        <label><input class="form-check-input me-2" type="checkbox" checked>Notify on new device login</label>
                        <label><input class="form-check-input me-2" type="checkbox" checked>Notify on repeated failed login</label>
                    </div>
                </div>
            </div>
        </section>

        <section class="card data-card settings-panel">
            <div class="table-title-row">
                <h5>Workflow Defaults</h5>
                <span class="table-meta">Keep daily processing screens aligned for the whole team.</span>
            </div>

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="card-label">Default Landing Page</label>
                    <select class="form-select">
                        <option selected>Dashboard</option>
                        <option>Pending Applications</option>
                        <option>Reports Dashboard</option>
                        <option>Employee List</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="card-label">Default Report Period</label>
                    <select class="form-select">
                        <option>Today</option>
                        <option selected>This Month</option>
                        <option>This Quarter</option>
                        <option>This Year</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="card-label">Operational Notes</label>
                    <textarea class="form-control" rows="4" placeholder="Add shared notes for dashboard users, review teams, or finance staff."></textarea>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
