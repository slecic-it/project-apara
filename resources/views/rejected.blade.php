<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>APARA | Reject Applications</title>

<!-- Bootstrap -->
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
            <h3 class="page-heading">Rejected Applications</h3>
            <p class="page-subtitle">Rejected records now use the same shared page structure without changing the data shown.</p>
        </div>
    </div>

    <!-- Search & Filter Section -->
    <div class="card filter-card mb-3">
        <div class="row g-2">
            <div class="col-md-3">
                <label class="card-label">ID Number</label>
                <input type="text" id="searchID" class="form-control" placeholder="Search by ID Number">
            </div>

            <div class="col-md-3">
                <label class="card-label">From Date</label>
                <input type="date" id="fromDate" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="card-label">To Date</label>
                <input type="date" id="toDate" class="form-control">
            </div>

            <div class="col-md-3">
                <button class="btn btn-danger w-100" onclick="filterData()">Search</button>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card data-card">
        <div class="table-title-row">
            <h5>Rejected Application Records</h5>
            <span class="table-meta">Existing details stay exactly the same.</span>
        </div>
        <table class="table table-bordered table-hover" id="appTable">
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
                    <th>Application Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="tableBody">
                <!-- Sample Data -->
                <tr>
                    <td>APP001</td>
                    <td>PR001</td>
                    <td>Kavisha Nimethmi</td>
                    <td>199812345678</td>
                    <td>UAE</td>
                    <td>P1234567</td>
                    <td>Yes</td>
                    <td>No</td>
                    <td>No</td>
                    <td>Yes</td>
                    <td>2026-02-01</td>
                    <td><button class="btn btn-sm btn-danger">Rejected</button></td>
                </tr>

                <tr>
                    <td>APP002</td>
                    <td>PR002</td>
                    <td>Nimal Perera</td>
                    <td>199045678912</td>
                    <td>Qatar</td>
                    <td>P9876543</td>
                    <td>No</td>
                    <td>Yes</td>
                    <td>No</td>
                    <td>No</td>
                    <td>2026-01-20</td>
                    <td><button class="btn btn-sm btn-danger">Rejected</button></td>
                </tr>
            </tbody>
        </table>
    </div>

</div>
</div>

<script>
function filterData() {
    let idInput = document.getElementById("searchID").value.toLowerCase();
    let fromDate = document.getElementById("fromDate").value;
    let toDate = document.getElementById("toDate").value;

    let table = document.getElementById("appTable");
    let rows = table.getElementsByTagName("tr");

    for (let i = 1; i < rows.length; i++) {
        let idCell = rows[i].cells[3].innerText.toLowerCase();
        let dateCell = rows[i].cells[10].innerText;

        let show = true;

        // ID Filter
        if (idInput && !idCell.includes(idInput)) {
            show = false;
        }

        // Date Filter
        if (fromDate && dateCell < fromDate) {
            show = false;
        }
        if (toDate && dateCell > toDate) {
            show = false;
        }

        rows[i].style.display = show ? "" : "none";
    }
}
</script>

</body>
</html>
