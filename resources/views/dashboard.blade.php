<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>APARA | Admin Dashboard</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

<style>
body{
    font-family: "Segoe UI", Arial, sans-serif;
    background:#f5f7fb;
    margin:0;
}

/* ================= SIDEBAR ================= */
.sidebar{
    width:240px;
    height:100vh;
    background:#ffffff;
    position:fixed;
    top:0;
    left:0;
    border-right:1px solid #ddd;
    padding-top:15px;
}
.sidebar .logo{
    font-size:20px;
    font-weight:bold;
    text-align:center;
    margin-bottom:20px;
}
.sidebar a{
    display:flex;
    align-items:center;
    padding:12px 18px;
    color:#333;
    text-decoration:none;
    font-size:14px;
}
.sidebar a:hover, .sidebar a.active{
    background:#e8f0ff;
    color:#2563eb;
}
.submenu{
    display:none;
    flex-direction:column;
    padding-left:20px;
}
.submenu a{
    font-size:13px;
    padding:8px 10px;
}

/* ================= HEADER ================= */
.header{
    position:fixed;
    left:240px;
    right:0;
    top:0;
    height:60px;
    background:#fff;
    border-bottom:1px solid #ddd;
    display:flex;
    justify-content:space-between;
    align-items:center;
    padding:0 20px;
    z-index:100;
}

/* ================= MAIN ================= */
.main{
    margin-left:240px;
    margin-top:70px;
    padding:20px;
}

/* ================= STATUS SUMMARY ================= */
.status-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(180px,1fr));
    gap:15px;
}
.status-box{
    background:#fff;
    border-radius:10px;
    padding:15px;
    box-shadow:0 2px 5px rgba(0,0,0,0.05);
}
.status-box h6{
    font-size:13px;
    color:#777;
}
.status-box h3{
    font-weight:bold;
}
.status-box.pending{border-left:5px solid #facc15;}
.status-box.accepted{border-left:5px solid #22c55e;}
.status-box.approved{border-left:5px solid #2563eb;}
.status-box.rejected{border-left:5px solid #ef4444;}
.status-box.payment{border-left:5px solid #f97316;}
.status-box.completed{border-left:5px solid #10b981;}
.status-box.finalized{border-left:5px solid #8b5cf6;}

/* ================= APPLICATION FLOW ================= */
.flow-card{
    background:#fff;
    padding:20px;
    border-radius:10px;
    box-shadow:0 2px 5px rgba(0,0,0,0.05);
}
.flow-steps{
    display:flex;
    flex-wrap:wrap;
    gap:15px;
}
.flow-step{
    flex:1;
    min-width:150px;
    border:1px solid #ddd;
    border-radius:10px;
    padding:12px;
    cursor:pointer;
    transition:0.2s;
}
.flow-step:hover{
    background:#f8fafc;
}
.flow-circle{
    width:30px;
    height:30px;
    border-radius:50%;
    text-align:center;
    line-height:30px;
    font-weight:bold;
    color:white;
}
.completed{background:#22c55e;}
.current{background:#2563eb;}
.pending{background:#64748b;}
.flow-actions{
    display:none;
    font-size:13px;
    margin-top:8px;
}
.flow-actions.show{
    display:block;
}

/* ================= TABLE ================= */
.table-card{
    background:#fff;
    border-radius:10px;
    box-shadow:0 2px 5px rgba(0,0,0,0.05);
}

/* ================= FOOTER ================= */
footer{
    margin-left:240px;
    background:#fff;
    border-top:1px solid #ddd;
    text-align:center;
    padding:10px;
    font-size:13px;
}
</style>
</head>

<body>

<!-- ================= SIDEBAR ================= -->
<div class="sidebar">
    <div class="logo">APARA Admin</div>

    <a class="active"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>

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

<h4>System Dashboard</h4>
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
© 2025 Sri Lanka Export Credit Insurance Corporation | APARA System
</footer>

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
</html>
