<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Employee Management</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
body { background:#f4f6f9; }
.card { border-radius:12px; }
</style>
</head>

<body>

<div class="container mt-4">

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3> Employee List</h3>
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
        <i class="bi bi-person-plus"></i> Add Employee
    </button>
</div>

<!-- Search & Export -->
<div class="row mb-3">
    <div class="col-md-4">
        <input type="text" id="searchID" class="form-control" placeholder="Search by Employee ID">
    </div>
    <div class="col-md-2">
        <button class="btn btn-success w-100" onclick="exportTable()">
            <i class="bi bi-file-earmark-excel"></i> Export
        </button>
    </div>
</div>

<!-- Employee Table -->
<div class="card p-3">
<table class="table table-bordered table-hover" id="employeeTable">
<thead class="table-dark">
<tr>
    <th>Employee ID</th>
    <th>Employee Name</th>
    <th>Designation</th>
    <th>Department</th>
    <th>Remove</th>
</tr>
</thead>
<tbody id="employeeBody">
</tbody>
</table>
</div>

</div>

<!-- Add Employee Modal -->
<div class="modal fade" id="addEmployeeModal">
<div class="modal-dialog">
<div class="modal-content">
<div class="modal-header">
<h5 class="modal-title">Add Employee</h5>
<button class="btn-close" data-bs-dismiss="modal"></button>
</div>

<div class="modal-body">
<input type="text" id="empID" class="form-control mb-2" placeholder="Employee ID">
<input type="text" id="empName" class="form-control mb-2" placeholder="Employee Name">
<input type="text" id="empDesignation" class="form-control mb-2" placeholder="Designation">
<input type="text" id="empDepartment" class="form-control mb-2" placeholder="Department">
</div>

<div class="modal-footer">
<button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
<button class="btn btn-primary" onclick="addEmployee()">Save</button>
</div>

</div>
</div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script>
let employees = [];

// Add Employee
function addEmployee() {
    let id = empID.value;
    let name = empName.value;
    let des = empDesignation.value;
    let dep = empDepartment.value;

    if(id === "" || name === "") {
        alert("Please enter Employee ID & Name");
        return;
    }

    employees.push({id, name, des, dep});
    renderTable();

    // Clear fields
    empID.value = empName.value = empDesignation.value = empDepartment.value = "";
    bootstrap.Modal.getInstance(document.getElementById('addEmployeeModal')).hide();
}

// Render Table
function renderTable() {
    let body = "";
    employees.forEach((e, index) => {
        body += `
        <tr>
            <td>${e.id}</td>
            <td>${e.name}</td>
            <td>${e.des}</td>
            <td>${e.dep}</td>
            <td>
                <button class="btn btn-danger btn-sm" onclick="removeEmployee(${index})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        </tr>`;
    });
    employeeBody.innerHTML = body;
}

// Remove Employee
function removeEmployee(index) {
    if(confirm("Delete this employee?")) {
        employees.splice(index,1);
        renderTable();
    }
}

// Search by Employee ID
document.getElementById("searchID").addEventListener("keyup", function() {
    let value = this.value.toLowerCase();
    let rows = document.querySelectorAll("#employeeTable tbody tr");

    rows.forEach(row => {
        let id = row.cells[0].textContent.toLowerCase();
        row.style.display = id.includes(value) ? "" : "none";
    });
});

// Export to Excel (CSV)
function exportTable() {
    let csv = "Employee ID,Employee Name,Designation,Department\n";
    employees.forEach(e => {
        csv += `${e.id},${e.name},${e.des},${e.dep}\n`;
    });

    let blob = new Blob([csv], { type: "text/csv" });
    let url = window.URL.createObjectURL(blob);

    let a = document.createElement("a");
    a.href = url;
    a.download = "employees.csv";
    a.click();
}
</script>

</body>
</html>
