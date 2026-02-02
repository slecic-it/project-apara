<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Pending Applications</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
body{
    background:#f4f6f9;
    font-family: Arial, sans-serif;
}
.card{
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}
table{
    background:white;
}
th{
    background:#0d6efd;
    color:white;
    text-align:center;
}
td{
    text-align:center;
}
</style>
</head>

<body>

<div class="container mt-4">

<h3 class="mb-3">📌 Pending Applications</h3>

<!-- Filter Section -->
<div class="card p-3 mb-3">
<div class="row g-2">

<div class="col-md-3">
<label>ID Number</label>
<input type="text" id="searchID" class="form-control" placeholder="Enter ID No">
</div>

<div class="col-md-3">
<label>From Date</label>
<input type="date" id="fromDate" class="form-control">
</div>

<div class="col-md-3">
<label>To Date</label>
<input type="date" id="toDate" class="form-control">
</div>

<div class="col-md-3 d-flex align-items-end">
<button class="btn btn-primary w-100" onclick="filterTable()">Search</button>
</div>

</div>
</div>


<!-- Pending Applications Table -->
<div class="card p-3">
<div class="table-responsive">
<table class="table table-bordered table-hover" id="appTable">
<thead>
<tr>
<th>Application No</th>
<th>Proposal No</th>
<th>Customer Name</th>
<th>ID No</th>
<th>Country</th>
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
<tr>
<td>APP001</td>
<td>PRO001</td>
<td>Kavisha Nimethmi</td>
<td>200012345678</td>
<td>UAE</td>
<td>N1234567</td>
<td>Yes</td>
<td>No</td>
<td>No</td>
<td>Yes</td>
<td>2026-02-01</td>
<td>
<button class="btn btn-success btn-sm">View</button>
<button class="btn btn-danger btn-sm">Reject</button>
</td>
</tr>

<tr>
<td>APP002</td>
<td>PRO002</td>
<td>John Silva</td>
<td>199845612345</td>
<td>Qatar</td>
<td>N9876543</td>
<td>No</td>
<td>Yes</td>
<td>No</td>
<td>No</td>
<td>2026-01-25</td>
<td>
<button class="btn btn-success btn-sm">View</button>
<button class="btn btn-danger btn-sm">Reject</button>
</td>
</tr>

</tbody>
</table>
</div>
</div>

</div>


<!-- JavaScript Filter -->
<script>
function filterTable() {

    let idInput = document.getElementById("searchID").value.toUpperCase();
    let fromDate = document.getElementById("fromDate").value;
    let toDate = document.getElementById("toDate").value;

    let table = document.getElementById("appTable");
    let rows = table.getElementsByTagName("tr");

    for (let i = 1; i < rows.length; i++) {
        let idCell = rows[i].getElementsByTagName("td")[3];
        let dateCell = rows[i].getElementsByTagName("td")[10];

        if (!idCell || !dateCell) continue;

        let idText = idCell.textContent || idCell.innerText;
        let rowDate = dateCell.textContent;

        let show = true;

        // ID Filter
        if (idInput && !idText.includes(idInput)) {
            show = false;
        }

        // Date Filter
        if (fromDate && rowDate < fromDate) show = false;
        if (toDate && rowDate > toDate) show = false;

        rows[i].style.display = show ? "" : "none";
    }
}
</script>

</body>
</html>
