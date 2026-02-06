<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>APARA | Reports</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
body {
    background:#ffffff;
    font-family: "Segoe UI", sans-serif;
}

/* Page Title */
.page-title {
    font-weight:700;
    font-size:22px;
    color:#0d6efd;
    border-bottom:2px solid #f1f1f1;
    padding-bottom:8px;
}

/* Cards */
.card {
    border: none;
    border-radius: 14px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    background:#ffffff;
}

/* Section Titles */
.section-title {
    font-weight:600;
    font-size:16px;
    color:#333;
    margin-bottom:10px;
    border-left:4px solid #0d6efd;
    padding-left:8px;
}

/* Table */
.table {
    background:#ffffff;
    border-radius:10px;
    overflow:hidden;
}
.table th {
    background:#f8f9fa !important;
    color:#333;
    font-weight:600;
}

/* Buttons */
.btn-primary {
    background:#0d6efd;
    border:none;
}
.btn-primary:hover {
    background:#0b5ed7;
}
</style>
</head>

<body>

<div class="container mt-4">

<h3 class="page-title mb-4"> Reports Dashboard</h3>

<!-- ================= ADVANCED RECORD FILTER ================= -->
<div class="card p-4 mb-4">
<div class="section-title">Advanced Record Filtering</div>

<div class="row g-3 mt-2">
    <div class="col-md-3">
        <label>Employee Name</label>
        <select class="form-select">
            <option>Select Employee</option>
            <option>John Silva</option>
            <option>Kavisha Nimethmi</option>
        </select>
    </div>

    <div class="col-md-3">
        <label>Bank Name</label>
        <select class="form-select">
            <option>Select Bank</option>
            <option>BOC</option>
            <option>People's Bank</option>
            <option>Commercial Bank</option>
        </select>
    </div>

    <div class="col-md-2">
        <label>From</label>
        <input type="date" class="form-control">
    </div>

    <div class="col-md-2">
        <label>To</label>
        <input type="date" class="form-control">
    </div>

    <div class="col-md-2 d-flex align-items-end">
        <button class="btn btn-primary w-100">Show Results</button>
    </div>
</div>
</div>

<!-- ================= EMPLOYEE ACTIVITY REPORT ================= -->
<div class="card p-4 mb-4">
<div class="section-title">Employee Activity Reports</div>

<div class="row text-center mt-2">
    <div class="col-md-4">
        <div class="p-3 border rounded bg-light">
            <h6>Accepted / Approved</h6>
            <h3 class="text-success">125</h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="p-3 border rounded bg-light">
            <h6>Rejected</h6>
            <h3 class="text-danger">15</h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="p-3 border rounded bg-light">
            <h6>Avg Turnaround Time</h6>
            <h3 class="text-primary">2.5 Days</h3>
        </div>
    </div>
</div>
</div>

<!-- ================= PROCESSING TIME REPORT ================= -->
<div class="card p-4 mb-4">
<div class="section-title">Processing Time and Involved Employee</div>

<div class="row g-3 mt-2">
    <div class="col-md-4">
        <input type="text" class="form-control" placeholder="Enter Application No">
    </div>
    <div class="col-md-2">
        <button class="btn btn-primary w-100">Show Results</button>
    </div>
</div>

<table class="table table-bordered mt-3">
<thead>
<tr>
    <th>Application No</th>
    <th>Employee Name</th>
    <th>Processing Time (Days)</th>
</tr>
</thead>
<tbody>
<tr>
    <td>APP-10234</td>
    <td>John Silva</td>
    <td>3</td>
</tr>
</tbody>
</table>
</div>

<!-- ================= OTHER REPORTS ================= -->
<div class="card p-4 mb-4">
<div class="section-title">Other Reports</div>

<div class="row g-3 mt-2">
    <div class="col-md-3">
        <button class="btn btn-outline-warning w-100">Pending After Payment</button>
    </div>
    <div class="col-md-3">
        <button class="btn btn-outline-success w-100">Guarantee Issuance</button>
    </div>
    <div class="col-md-3">
        <button class="btn btn-outline-danger w-100">Issuance Pending</button>
    </div>
</div>
</div>

<!-- ================= BANKWISE APPLICATION SUMMARY ================= -->
<div class="card p-4 mb-4">
<div class="section-title">Bankwise Application Summary</div>

<div class="row g-3 mt-2">
    <div class="col-md-3">
        <label>From</label>
        <input type="date" class="form-control">
    </div>
    <div class="col-md-3">
        <label>To</label>
        <input type="date" class="form-control">
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <button class="btn btn-primary w-100">Generate</button>
    </div>
</div>

<table class="table table-bordered mt-3">
<thead>
<tr>
    <th>Bank Name</th>
    <th>Total Applications</th>
    <th>Approved</th>
    <th>Rejected</th>
</tr>
</thead>
<tbody>
<tr>
    <td>BOC</td>
    <td>120</td>
    <td>105</td>
    <td>15</td>
</tr>
</tbody>
</table>
</div>

<!-- ================= STATUS REPORT ================= -->
<div class="card p-4 mb-4">
<div class="section-title">Status Reports</div>

<div class="row g-3 mt-2">
    <div class="col-md-3">
        <label>From</label>
        <input type="date" class="form-control">
    </div>
    <div class="col-md-3">
        <label>To</label>
        <input type="date" class="form-control">
    </div>
    <div class="col-md-2 d-flex align-items-end">
        <button class="btn btn-primary w-100">Generate</button>
    </div>
</div>

<table class="table table-bordered mt-3">
<thead>
<tr>
    <th>Status</th>
    <th>Count</th>
</tr>
</thead>
<tbody>
<tr><td>Pending</td><td>45</td></tr>
<tr><td>Approved</td><td>210</td></tr>
<tr><td>Rejected</td><td>25</td></tr>
</tbody>
</table>
</div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
