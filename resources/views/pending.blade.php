<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>APARA | Pending Applications</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<link rel="stylesheet" href="{{ asset('css/styles.css') }}">

</head>

<body>
@include('layouts.sidebar')
@include('layouts.header')

<div class="main with-sidebar">
<div class="page-shell">

    <!-- Header -->
    <div class="page-header">
        <div class="page-header-content">
        <h4 class="page-heading">
            <i class="bi bi-hourglass-split text-warning me-2"></i>
            Pending Applications
        </h4>
        <p class="page-subtitle">Monitor pending applications using the same design language as the other Application, Management, and Finance pages.</p>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="card filter-card p-4 mb-4">
        <div class="row g-3">

            <div class="col-md-4">
                <label class="card-label">Search by ID Number</label>
                <input type="text" id="searchID" class="form-control" placeholder="Enter ID No">
            </div>

            <div class="col-md-3">
                <label class="card-label">From Date</label>
                <input type="date" id="fromDate" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="card-label">To Date</label>
                <input type="date" id="toDate" class="form-control">
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100" onclick="filterTable()">
                    <i class="bi bi-search me-1"></i> Search
                </button>
            </div>

        </div>
    </div>

    <!-- Table Section -->
    <div class="data-card table-container">
        <div class="table-title-row">
            <h5>Pending Application Records</h5>
            <span class="table-meta">Existing details stay exactly the same.</span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle" id="appTable">

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>ID Number</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>

                @foreach($applications as $app)
                <tr>
                    <td>{{ $app->id }}</td>
                    <td>{{ $app->name }}</td>
                    <td>{{ $app->id_number }}</td>
                    <td>{{ \Carbon\Carbon::parse($app->created_at)->format('Y-m-d') }}</td>
                    <td>
                        <span class="badge badge-pending">
                            <i class="bi bi-hourglass me-1"></i> Pending
                        </span>
                    </td>
                </tr>
                @endforeach

                </tbody>

            </table>
        </div>
    </div>

</div>
</div>

<!-- JavaScript -->
<script>

function filterTable() {

    let idInput = document.getElementById("searchID").value.toLowerCase();
    let fromDate = document.getElementById("fromDate").value;
    let toDate = document.getElementById("toDate").value;

    let rows = document.querySelectorAll("#appTable tbody tr");

    rows.forEach(row => {

        let idNumber = row.cells[2].innerText.toLowerCase();
        let rowDate = row.cells[3].innerText;

        let show = true;

        if (idInput && !idNumber.includes(idInput)) show = false;
        if (fromDate && rowDate < fromDate) show = false;
        if (toDate && rowDate > toDate) show = false;

        row.style.display = show ? "" : "none";

    });
}

</script>

</body>
</html>
