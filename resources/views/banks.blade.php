<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>APARA | Banks</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
body { background:#f8f9fa; }
.card { border-radius:15px; box-shadow:0 5px 15px rgba(0,0,0,0.08); }
</style>
</head>

<body class="p-4">

<div class="container-fluid">

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
<h4><i class="bi bi-bank"></i> Banks</h4>
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBankModal">
<i class="bi bi-plus-circle"></i> Add Bank
</button>
</div>

<!-- Search & Export -->
<div class="row mb-3">
<div class="col-md-4">
<input type="text" id="searchBranch" class="form-control" placeholder="Search by Branch Name">
</div>
<div class="col-md-8 text-end">
<button class="btn btn-success" onclick="exportTableToCSV()">
<i class="bi bi-file-earmark-excel"></i> Export Bank Details
</button>
</div>
</div>

<!-- Banks Table -->
<div class="card p-3">
<div class="table-responsive">
<table class="table table-bordered table-hover" id="banksTable">
<thead class="table-dark">
<tr>
<th>Bank Name</th>
<th>Bank Code</th>
<th>Branch Name</th>
<th>Branch Grade</th>
<th>Province</th>
<th>Email</th>
<th>Tel No</th>
<th>Action</th>
</tr>
</thead>
<tbody id="bankData">
</tbody>
</table>
</div>
</div>

</div>

<!-- Add Bank Modal -->
<div class="modal fade" id="addBankModal">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<div class="modal-header bg-primary text-white">
<h5 class="modal-title">Add Bank Details</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<div class="row g-2">
<div class="col-md-6">
<label>Bank Name</label>
<input type="text" id="bankName" class="form-control">
</div>

<div class="col-md-6">
<label>Bank Code</label>
<input type="text" id="bankCode" class="form-control">
</div>

<div class="col-md-6">
<label>Branch Name</label>
<input type="text" id="branchName" class="form-control">
</div>

<div class="col-md-6">
<label>Branch Grade</label>
<select id="branchGrade" class="form-control">
<option>A</option>
<option>B</option>
<option>C</option>
</select>
</div>

<div class="col-md-6">
<label>Province</label>
<select id="province" class="form-control">
<option>Western</option>
<option>Central</option>
<option>Southern</option>
<option>Northern</option>
<option>Eastern</option>
<option>North Western</option>
<option>North Central</option>
<option>Uva</option>
<option>Sabaragamuwa</option>
</select>
</div>

<div class="col-md-6">
<label>Email</label>
<input type="email" id="email" class="form-control">
</div>

<div class="col-md-6">
<label>Tel No</label>
<input type="text" id="tel" class="form-control">
</div>
</div>
</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
<button class="btn btn-success" onclick="addBank()">Save Bank</button>
</div>

</div>
</div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
let banks = [];

// Add Bank
function addBank() {
let bank = {
bankName: bankName.value,
bankCode: bankCode.value,
branchName: branchName.value,
branchGrade: branchGrade.value,
province: province.value,
email: email.value,
tel: tel.value
};

banks.push(bank);
renderTable();
document.querySelector("#addBankModal .btn-close").click();
}

// Render Table
function renderTable() {
let html = "";
banks.forEach((b, i) => {
html += `<tr>
<td>${b.bankName}</td>
<td>${b.bankCode}</td>
<td>${b.branchName}</td>
<td>${b.branchGrade}</td>
<td>${b.province}</td>
<td>${b.email}</td>
<td>${b.tel}</td>
<td>
<button class="btn btn-sm btn-danger" onclick="deleteBank(${i})">
<i class="bi bi-trash"></i>
</button>
</td>
</tr>`;
});
document.getElementById("bankData").innerHTML = html;
}

// Delete Bank
function deleteBank(index) {
banks.splice(index, 1);
renderTable();
}

// Search Branch Name
document.getElementById("searchBranch").addEventListener("keyup", function() {
let filter = this.value.toLowerCase();
let rows = document.querySelectorAll("#bankData tr");
rows.forEach(row => {
row.style.display = row.innerText.toLowerCase().includes(filter) ? "" : "none";
});
});

// Export CSV
function exportTableToCSV() {
let csv = [];
let rows = document.querySelectorAll("#banksTable tr");

rows.forEach(row => {
let cols = row.querySelectorAll("td, th");
let data = [];
cols.forEach(col => data.push(col.innerText));
csv.push(data.join(","));
});

let blob = new Blob([csv.join("\n")], { type: "text/csv" });
let url = window.URL.createObjectURL(blob);
let a = document.createElement("a");
a.href = url;
a.download = "banks_details.csv";
a.click();
}
</script>

</body>
</html>
