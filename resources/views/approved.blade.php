<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>APARA | Application Approval</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/styles.css') }}"/>
</head>

<body>
@include('layouts.sidebar')
@include('layouts.header')

<div class="main with-sidebar">
<div class="page-shell">

<div class="page-header">
<div class="page-header-content">
<h3 class="page-heading">Application Approval Panel</h3>
<p class="page-subtitle">Click the application number to open the full saved application view.</p>
</div>
</div>

<div class="card filter-card mb-3">
<div class="row g-3 align-items-end">
    <div class="col-lg-3 col-md-6">
        <label class="card-label">ID Number</label>
        <input type="text" id="searchID" class="form-control" placeholder="Search by ID Number">
    </div>

    <div class="col-lg-3 col-md-6">
        <label class="card-label">From Date</label>
        <input type="date" id="fromDate" class="form-control">
    </div>

    <div class="col-lg-3 col-md-6">
        <label class="card-label">To Date</label>
        <input type="date" id="toDate" class="form-control">
    </div>

    <div class="col-lg-3 col-md-6">
        <label class="card-label">Application Date</label>
        <input type="date" id="appDate" class="form-control">
    </div>

    <div class="col-12">
        <div class="d-flex justify-content-end gap-2 flex-wrap">
            <button class="btn btn-primary" onclick="filterTable()">Filter</button>
            <button class="btn btn-secondary" onclick="resetFilter()">Reset</button>
        </div>
    </div>
</div>
</div>

<div class="card data-card">
<div class="table-title-row">
<h5>Approval Records</h5>
<span class="table-meta">Showing {{ $applications->count() }} approved application{{ $applications->count() === 1 ? '' : 's' }}.</span>
</div>
<div class="table-responsive">
<table class="table table-bordered table-striped" id="appTable">
<thead>
<tr>
<th>Application No</th>
<th>Proposal No</th>
<th>Customer Name</th>
<th>ID No</th>
<th>Country of Employment</th>
<th>Passport</th>
<th>TAQ</th>
<th>RL</th>
<th>RAL</th>
<th>SLBFE Letter</th>
<th>Date</th>
<th>Action</th>
</tr>
</thead>

<tbody>
@forelse ($applications as $application)
<tr>
<td>
    <a href="{{ route('application.show', $application->id) }}" class="application-number-link">
        APP-{{ str_pad((string) $application->id, 4, '0', STR_PAD_LEFT) }}
    </a>
</td>
<td>{{ $application->proposal_no ?? '-' }}</td>
<td>{{ $application->full_name ?? $application->name_with_initials ?? '-' }}</td>
<td>{{ $application->nic ?? '-' }}</td>
<td>{{ $application->country_id ?? '-' }}</td>
<td>{{ $application->passport_no ?? '-' }}</td>
<td>{{ $application->taq_status ?? 'N/A' }}</td>
<td>{{ $application->rl_status ?? 'N/A' }}</td>
<td>{{ $application->ral_status ?? 'N/A' }}</td>
<td>{{ $application->slbfe_letter_status ?? 'N/A' }}</td>
<td>{{ !empty($application->created_at) ? \Illuminate\Support\Carbon::parse($application->created_at)->format('Y-m-d') : '-' }}</td>
<td>
<div class="d-grid gap-2">
<a href="{{ route('application.show', $application->id) }}" class="btn btn-success btn-sm">View</a>
</div>
</td>
</tr>
@empty
<tr>
<td colspan="12" class="text-center text-muted py-4">No approved applications available.</td>
</tr>
@endforelse
</tbody>
</table>
</div>
</div>

</div>
</div>

<script>
function filterTable() {
    let searchID = document.getElementById("searchID").value.toLowerCase();
    let fromDate = document.getElementById("fromDate").value;
    let toDate = document.getElementById("toDate").value;
    let appDate = document.getElementById("appDate").value;

    let rows = document.querySelectorAll("#appTable tbody tr");

    rows.forEach(row => {
        if (row.cells.length < 11) {
            return;
        }

        let id = row.cells[3].innerText.toLowerCase();
        let date = row.cells[10].innerText.trim();
        let show = true;

        if (searchID && !id.includes(searchID)) show = false;
        if (fromDate && date !== "-" && date < fromDate) show = false;
        if (toDate && date !== "-" && date > toDate) show = false;
        if (appDate && date !== appDate) show = false;

        row.style.display = show ? "" : "none";
    });
}

function resetFilter() {
    document.getElementById("searchID").value = "";
    document.getElementById("fromDate").value = "";
    document.getElementById("toDate").value = "";
    document.getElementById("appDate").value = "";

    let rows = document.querySelectorAll("#appTable tbody tr");
    rows.forEach(row => row.style.display = "");
}
</script>

</body>
</html>
