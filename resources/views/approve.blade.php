<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>APARA | Application Approval</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/style.css"/>

<style>

</style>
</head>

<body>

<div class="container mt-4">

<h3 class="mb-3"> APARA Application Approval Panel</h3>

<!-- 🔍 Filter Section -->
<div class="card p-3 mb-3">
<div class="row g-2">

<div class="col-md-3">
<input type="text" id="searchID" class="form-control" placeholder="Search by ID Number">
</div>

<div class="col-md-3">
<label>From Date</label>
<input type="date" id="fromDate" class="form-control">
</div>

<div class="col-md-3">
<label>To Date</label>
<input type="date" id="toDate" class="form-control">
</div>

<div class="col-md-3">
<label>Application Date</label>
<input type="date" id="appDate" class="form-control">
</div>

<div class="col-md-12 text-end">
<button class="btn btn-primary mt-2" onclick="filterTable()">🔍 Filter</button>
<button class="btn btn-secondary mt-2" onclick="resetFilter()">Reset</button>
</div>

</div>
</div>


<!-- 📊 Application Table -->
<div class="card p-3">
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
<tr>
<td>APP001</td>
<td>PROP101</td>
<td>Kavisha Nimethmi</td>
<td>200012345678</td>
<td>UAE</td>
<td>N1234567</td>
<td>Yes</td>
<td>Yes</td>
<td>No</td>
<td>Uploaded</td>
<td>2026-02-01</td>
<td>
<button class="btn btn-success btn-sm">Approve</button>
<button class="btn btn-danger btn-sm">Reject</button>
</td>
</tr>

<tr>
<td>APP002</td>
<td>PROP102</td>
<td>Amal Perera</td>
<td>199923456789</td>
<td>Qatar</td>
<td>N9988776</td>
<td>No</td>
<td>Yes</td>
<td>Yes</td>
<td>Uploaded</td>
<td>2026-01-30</td>
<td>
<button class="btn btn-success btn-sm">Approve</button>
<button class="btn btn-danger btn-sm">Reject</button>
</td>
</tr>

</tbody>
</table>
</div>

</div>

<!-- JS -->
<script>
function filterTable() {
    let searchID = document.getElementById("searchID").value.toLowerCase();
    let fromDate = document.getElementById("fromDate").value;
    let toDate = document.getElementById("toDate").value;
    let appDate = document.getElementById("appDate").value;

    let rows = document.querySelectorAll("#appTable tbody tr");

    rows.forEach(row => {
        let id = row.cells[3].innerText.toLowerCase();
        let date = row.cells[10].innerText;

        let show = true;

        if (searchID && !id.includes(searchID)) show = false;
        if (fromDate && date < fromDate) show = false;
        if (toDate && date > toDate) show = false;
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
