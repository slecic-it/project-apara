@include('layouts.header');
</head>
<body>

<!-- ================= SIDEBAR ================= -->
<div class="sidebar">
    <div class="logo">{{ $employee['name'] }}</div>

    <a class="active"><i class="bi bi-speedometer2 me-2"></i> Dashboard </a>

    <a onclick="toggleMenu('appMenu')"><i class="bi bi-folder me-2"></i> Applications <i class="bi bi-chevron-down ms-auto"></i></a>
    <div class="submenu" id="appMenu">
        <a>Pending</a>
        <a>Accepted</a>
        <a>Approved</a>
        <a>Rejected</a>
    </div>

    <a onclick="toggleMenu('manageMenu')"><i class="bi bi-gear me-2"></i> Management <i class="bi bi-chevron-down ms-auto"></i></a>
    <div class="submenu" id="manageMenu">
        <a>Banks</a>
        <a>Employees</a>
        <a>Reports</a>
    </div>

    <a onclick="toggleMenu('financeMenu')"><i class="bi bi-cash me-2"></i> Finance <i class="bi bi-chevron-down ms-auto"></i></a>
    <div class="submenu" id="financeMenu">
        <a>Invoice</a>
        <a>Fee Due</a>
        <a>Receipt</a>
        <a>History</a>
    </div>
</div>

<!-- ================= HEADER ================= -->
<div class="header">
    <div id="datetime"></div>
    <div><i class="bi bi-bell me-3 text-primary"></i> Admin User</div>
</div>

<!-- ================= MAIN ================= -->
<div class="main">

<h4>Dashboard</h4>
<p class="text-muted">Real-time APARA system monitoring</p>

<!-- ================= STATUS SUMMARY ================= -->
<div class="status-grid mb-4">
    <div class="status-box pending"><h6>Pending</h6><h3 id="pendingCount">0</h3></div>
    <div class="status-box accepted"><h6>Accepted</h6><h3 id="acceptedCount">0</h3></div>
    <div class="status-box approved"><h6>Approved</h6><h3 id="approvedCount">0</h3></div>
    <div class="status-box rejected"><h6>Rejected</h6><h3 id="rejectedCount">0</h3></div>
    <div class="status-box payment"><h6>Payment Pending</h6><h3 id="paymentPendingCount">0</h3></div>
    <div class="status-box completed"><h6>Completed Payments</h6><h3 id="completedCount">0</h3></div>
    <div class="status-box finalized"><h6>Finalized</h6><h3 id="finalizedCount">0</h3></div>
</div>

<!-- ================= APPLICATION FLOW ================= -->
<div class="flow-card mb-4">
<h5>Application Workflow</h5>
<div class="flow-steps">

<div class="flow-step" data-step="submission">
<div class="flow-circle completed">1</div>
<b>Submission</b>
<div class="flow-actions" id="submissionActions">Fill form<br>Upload documents<br>Submit</div>
</div>

<div class="flow-step" data-step="validation">
<div class="flow-circle current">2</div>
<b>Validation</b>
<div class="flow-actions" id="validationActions">Verify data<br>Check documents</div>
</div>

<div class="flow-step" data-step="review">
<div class="flow-circle pending">3</div>
<b>Review</b>
<div class="flow-actions" id="reviewActions">Assign reviewer<br>Evaluate</div>
</div>

<div class="flow-step" data-step="approval">
<div class="flow-circle pending">4</div>
<b>Approval</b>
<div class="flow-actions" id="approvalActions">Approve / Reject</div>
</div>

<div class="flow-step" data-step="processing">
<div class="flow-circle pending">5</div>
<b>Processing</b>
<div class="flow-actions" id="processingActions">Issue documents</div>
</div>

<div class="flow-step" data-step="completion">
<div class="flow-circle pending">6</div>
<b>Completion</b>
<div class="flow-actions" id="completionActions">Archive & Close</div>
</div>

</div>
</div>

<!-- ================= APPLICATION TABLE ================= -->
<div class="table-card p-3">
<h5>Applications List</h5>

<table class="table table-hover mt-2">
<thead class="table-light">
<tr>
<th>App No</th>
<th>Proposal</th>
<th>Name</th>
<th>ID</th>
<th>Status</th>
<th>Actions</th>
</tr>
</thead>
<tbody id="applicationTable">
<tr>
<td>APP-0012</td>
<td>PR-556</td>
<td>ABC Exports</td>
<td>902134567V</td>
<td><span class="badge bg-warning">Pending</span></td>
<td>
<i class="bi bi-eye me-2"></i>
<i class="bi bi-pencil-square me-2"></i>
<i class="bi bi-credit-card"></i>
</td>
</tr>
</tbody>
</table>

</div>

</div>

<!-- ================= FOOTER ================= -->
<footer>
<script>
// Sidebar menu
function toggleMenu(id){
    let m=document.getElementById(id);
    m.style.display=(m.style.display==="flex")?"none":"flex";
}

// Date & Time
function updateDateTime(){
    document.getElementById("datetime").innerText=new Date().toLocaleString();
}
setInterval(updateDateTime,1000); updateDateTime();

// Flow click
document.querySelectorAll(".flow-step").forEach(step=>{
    step.onclick=()=>{
        document.querySelectorAll(".flow-actions").forEach(a=>a.classList.remove("show"));
        document.getElementById(step.dataset.step+"Actions").classList.add("show");
    };
});

// Status count demo
function updateStatusCounts(){
    let rows=document.querySelectorAll("#applicationTable tr");
    document.getElementById("pendingCount").innerText=rows.length;
}
updateStatusCounts();
</script>

</body>
@include('layouts.footer');