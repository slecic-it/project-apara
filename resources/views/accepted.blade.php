<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>APARA | Accepted Applications</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/style.css"/>

<style>

</style>
</head>

<body>

<div class="container mt-4">

    <h3 class="mb-3"> Accepted Applications</h3>

    <!-- Filters -->
    <div class="card p-3 mb-4">
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

            <div class="col-md-3 d-flex align-items-end">
                <button class="btn btn-primary w-100" onclick="filterApplications()">Filter</button>
            </div>

        </div>
    </div>


    <!-- Applications Table -->
    <div class="card p-3">
        <table class="table table-bordered table-striped" id="appTable">
            <thead class="table-dark">
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
            <tbody id="appBody"></tbody>
        </table>
    </div>

</div>

<script>

// Sample Data (Replace with DB/API)
let applications = [
{
    appNo:"APP001",
    proposalNo:"PRO001",
    name:"Kavisha Nimethmi",
    id:"199812345678",
    country:"Qatar",
    passport:"N1234567",
    taq:"Yes",
    rl:"Yes",
    ral:"Yes",
    slbfe:"Received",
    date:"2026-02-01"
},
{
    appNo:"APP002",
    proposalNo:"PRO002",
    name:"Nimal Perera",
    id:"199023456789",
    country:"UAE",
    passport:"N2345678",
    taq:"No",
    rl:"Yes",
    ral:"No",
    slbfe:"Pending",
    date:"2026-01-25"
}
];

// Load Table
function loadTable(data){
    let tbody = document.getElementById("appBody");
    tbody.innerHTML = "";

    data.forEach(app=>{
        tbody.innerHTML += `
        <tr>
            <td>${app.appNo}</td>
            <td>${app.proposalNo}</td>
            <td>${app.name}</td>
            <td>${app.id}</td>
            <td>${app.country}</td>
            <td>${app.passport}</td>
            <td>${app.taq}</td>
            <td>${app.rl}</td>
            <td>${app.ral}</td>
            <td>${app.slbfe}</td>
            <td>${app.date}</td>
            <td>
                <button class="btn btn-sm btn-success">View</button>
            </td>
        </tr>`;
    });
}

// Filter Function
function filterApplications(){
    let idSearch = document.getElementById("searchID").value;
    let fromDate = document.getElementById("fromDate").value;
    let toDate = document.getElementById("toDate").value;

    let filtered = applications.filter(app=>{
        let matchID = idSearch === "" || app.id.includes(idSearch);
        let matchFrom = fromDate === "" || app.date >= fromDate;
        let matchTo = toDate === "" || app.date <= toDate;
        return matchID && matchFrom && matchTo;
    });

    loadTable(filtered);
}

// Initial Load
loadTable(applications);

</script>

</body>
</html>
