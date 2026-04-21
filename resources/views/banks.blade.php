<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>APARA | Banks</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/styles.css') }}"/>

</head>

<body>
@include('layouts.sidebar')
@include('layouts.header')

<div class="main with-sidebar">
<div class="page-shell">

@if(session('success'))
<div class="alert alert-success mb-3">{{ session('success') }}</div>
@endif

@if($errors->any())
<div class="alert alert-danger mb-3">{{ $errors->first() }}</div>
@endif

<!-- Page Header -->
<div class="page-header">
<div class="page-header-content">
<h4 class="page-heading"><i class="bi bi-bank me-2"></i>Banks</h4>
<p class="page-subtitle">Manage bank and branch records with the same consistent layout used across the system.</p>
</div>
<div class="page-actions">
<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBankModal">
<i class="bi bi-plus-circle"></i> Add Bank
</button>
</div>
</div>

<!-- Search & Export -->
<div class="card filter-card mb-3">
<div class="row mb-0 g-3">
<div class="col-md-4">
<label class="card-label">Bank</label>
<select id="bankFilter" class="form-control">
<option value="all">All Banks</option>
</select>
</div>
<div class="col-md-4">
<label class="card-label">Branch Name</label>
<input type="text" id="searchBranch" class="form-control" placeholder="Search by Branch Name">
</div>
<div class="col-md-4 text-end">
<button class="btn btn-success" onclick="exportTableToCSV()">
<i class="bi bi-file-earmark-excel"></i> Export Bank Details
</button>
</div>
</div>
</div>

<!-- Banks Table -->
<div class="card data-card">
<div class="table-title-row">
<h5>Bank Directory</h5>
<span class="table-meta">Existing bank details stay exactly the same.</span>
</div>
<div class="table-responsive">
<table class="table table-bordered table-hover" id="banksTable">
<thead class="table-dark">
<tr>
<th>Logo</th>
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

<form method="POST" id="deleteBankForm" style="display:none;">
@csrf
@method('DELETE')
</form>

<!-- Add Bank Modal -->
<div class="modal fade" id="addBankModal">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<form method="POST" action="{{ route('banks.store') }}" enctype="multipart/form-data">
@csrf
<div class="modal-header bg-primary text-white">
<h5 class="modal-title">Add Bank Details</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<div class="row g-2">
<div class="col-md-6">
<label>Bank Name</label>
<input type="text" id="bankName" name="bank_name" class="form-control" required>
</div>

<div class="col-md-6">
<label>Bank Code</label>
<input type="text" id="bankCode" name="bank_code" class="form-control">
</div>

<div class="col-md-6">
<label>Branch Name</label>
<input type="text" id="branchName" name="branch_name" class="form-control" required>
</div>

<div class="col-md-6">
<label>Branch Grade</label>
<select id="branchGrade" name="branch_grade" class="form-control">
<option>A</option>
<option>B</option>
<option>C</option>
</select>
</div>

<div class="col-md-6">
<label>Province</label>
<select id="province" name="province" class="form-control">
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
<input type="email" id="email" name="email" class="form-control" required>
</div>

<div class="col-md-6">
<label>Login Password</label>
<input type="password" id="password" name="password" class="form-control" minlength="8" required>
</div>

<div class="col-md-6">
<label>Tel No</label>
<input type="text" id="tel" name="tel" class="form-control">
</div>

<div class="col-md-6">
<label>Bank Logo</label>
<input type="file" id="bankLogo" name="bank_logo" class="form-control" accept="image/*">
</div>
</div>
</div>

<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
<button type="submit" class="btn btn-success">Save Bank</button>
</div>
</form>
</div>
</div>
</div>

<!-- Edit Bank Modal -->
<div class="modal fade" id="editBankModal">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<form method="POST" id="editBankForm" enctype="multipart/form-data">
@csrf
@method('PUT')
<div class="modal-header bg-primary text-white">
<h5 class="modal-title">Edit Bank Details</h5>
<button type="button" class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<div class="row g-2">
<div class="col-md-6">
<label>Bank Name</label>
<input type="text" id="editBankName" name="bank_name" class="form-control" required>
</div>

<div class="col-md-6">
<label>Bank Code</label>
<input type="text" id="editBankCode" name="bank_code" class="form-control">
</div>

<div class="col-md-6">
<label>Branch Name</label>
<input type="text" id="editBranchName" name="branch_name" class="form-control">
</div>

<div class="col-md-6">
<label>Branch Grade</label>
<select id="editBranchGrade" name="branch_grade" class="form-control">
<option value="A">A</option>
<option value="B">B</option>
<option value="C">C</option>
</select>
</div>

<div class="col-md-6">
<label>Province</label>
<select id="editProvince" name="province" class="form-control">
<option value="Western">Western</option>
<option value="Central">Central</option>
<option value="Southern">Southern</option>
<option value="Northern">Northern</option>
<option value="Eastern">Eastern</option>
<option value="North Western">North Western</option>
<option value="North Central">North Central</option>
<option value="Uva">Uva</option>
<option value="Sabaragamuwa">Sabaragamuwa</option>
</select>
</div>

<div class="col-md-6">
<label>Email</label>
<input type="email" id="editEmail" name="email" class="form-control" required>
</div>

<div class="col-md-6">
<label>New Password</label>
<input type="password" id="editPassword" name="password" class="form-control" minlength="8" placeholder="Leave blank to keep current password">
</div>

<div class="col-md-6">
<label>Tel No</label>
<input type="text" id="editTel" name="tel" class="form-control">
</div>

<div class="col-md-6">
<label>Replace Bank Logo</label>
<input type="file" id="editBankLogo" name="bank_logo" class="form-control" accept="image/*">
</div>
</div>
</div>

<div class="modal-footer">
<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
<button type="submit" class="btn btn-success">Update Bank</button>
</div>
</form>
</div>
</div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
@php
    $bankRows = collect($banks ?? [])->map(function ($bank) {
        return [
            'id' => $bank->id,
            'logoUrl' => $bank->logo_url ?? null,
            'bankName' => $bank->bank_name ?? $bank->name ?? '',
            'bankCode' => $bank->bank_code ?? $bank->code ?? '',
            'branchName' => $bank->branch_name ?? $bank->branch ?? '',
            'branchGrade' => $bank->branch_grade ?? $bank->grade ?? '',
            'province' => $bank->province ?? '',
            'email' => $bank->email ?? '',
            'tel' => $bank->tel ?? $bank->telephone ?? $bank->contact ?? '',
        ];
    })->values();
@endphp

let banks = @json($bankRows);

function filteredBanks() {
const selectedBank = (document.getElementById("bankFilter")?.value || "all").toLowerCase();
const branchSearch = (document.getElementById("searchBranch")?.value || "").toLowerCase().trim();

return banks.filter(bank => {
const matchesBank = selectedBank === "all" || (bank.bankName || "").toLowerCase() === selectedBank;
const matchesBranch = branchSearch === ""
    || `${bank.bankName || ""} ${bank.branchName || ""} ${bank.bankCode || ""} ${bank.province || ""} ${bank.email || ""} ${bank.tel || ""}`.toLowerCase().includes(branchSearch);

return matchesBank && matchesBranch;
});
}

function populateBankFilter() {
const bankFilter = document.getElementById("bankFilter");
if(!bankFilter) {
    return;
}

const currentValue = bankFilter.value || "all";
const bankNames = [...new Set(
    banks
        .map(bank => (bank.bankName || "").trim())
        .filter(Boolean)
        .sort((a, b) => a.localeCompare(b))
)];

bankFilter.innerHTML = `<option value="all">All Banks</option>`;
bankNames.forEach(bankName => {
    const option = document.createElement("option");
    option.value = bankName.toLowerCase();
    option.textContent = bankName;
    bankFilter.appendChild(option);
});

bankFilter.value = bankNames.some(bankName => bankName.toLowerCase() === currentValue) ? currentValue : "all";
}

function renderTable() {
let html = "";
filteredBanks().forEach((b) => {
const index = banks.findIndex(bank => bank.id === b.id);
html += `<tr>
<td>${b.logoUrl ? `<img src="${b.logoUrl}" alt="${b.bankName}" class="bank-directory-logo">` : `<span class="bank-directory-logo-fallback"><i class="bi bi-bank2"></i></span>`}</td>
<td>${b.bankName}</td>
<td>${b.bankCode}</td>
<td>${b.branchName}</td>
<td>${b.branchGrade}</td>
<td>${b.province}</td>
<td>${b.email}</td>
<td>${b.tel}</td>
<td>
<button class="btn btn-sm btn-primary me-2" onclick="openEditBankModal(${index})">
<i class="bi bi-pencil-square"></i>
</button>
<button class="btn btn-sm btn-danger" type="button" onclick="deleteBank(${index})">
<i class="bi bi-trash"></i>
</button>
</td>
</tr>`;
});
document.getElementById("bankData").innerHTML = html || `<tr><td colspan="9" class="text-center text-muted py-4">No bank records match the selected filter.</td></tr>`;
}

// Delete Bank
function deleteBank(index) {
let bank = banks[index];
if(!bank || !bank.id) {
    return;
}

if(!confirm(`Are you sure you want to delete ${bank.bankName || 'this bank'}?`)) {
    return;
}

const deleteForm = document.getElementById("deleteBankForm");
deleteForm.action = `{{ url('/banks') }}/${bank.id}`;
deleteForm.submit();
}

function openEditBankModal(index) {
let bank = banks[index];
if(!bank || !bank.id) {
    return;
}

document.getElementById("editBankForm").action = `{{ url('/banks') }}/${bank.id}`;
document.getElementById("editBankName").value = bank.bankName || "";
document.getElementById("editBankCode").value = bank.bankCode || "";
document.getElementById("editBranchName").value = bank.branchName || "";
document.getElementById("editBranchGrade").value = bank.branchGrade || "A";
document.getElementById("editProvince").value = bank.province || "Western";
document.getElementById("editEmail").value = bank.email || "";
document.getElementById("editTel").value = bank.tel || "";
document.getElementById("editBankLogo").value = "";
document.getElementById("editPassword").value = "";

const editModal = new bootstrap.Modal(document.getElementById("editBankModal"));
editModal.show();
}

document.getElementById("searchBranch").addEventListener("keyup", renderTable);
document.getElementById("bankFilter").addEventListener("change", renderTable);

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

populateBankFilter();
renderTable();
</script>

</body>
</html>
