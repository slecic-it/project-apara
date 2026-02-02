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
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f8f9fa;
}

/* SIDEBAR */
.sidebar {
    width: 220px;
    height: 100vh;
    background-color: #ffffff; /* White background */
    position: fixed;
    top: 0;
    left: 0;
    display: flex;
    flex-direction: column;
    color: #1e293b; /* Dark text */
    padding-top: 1rem;
    border-right: 1px solid #ddd;
}
.sidebar .logo {
    font-weight: bold;
    font-size: 1.2rem;
    text-align: center;
    margin-bottom: 2rem;
    color: #2563eb; /* Logo blue */
}
.sidebar a {
    color: #1e293b;
    padding: 0.75rem 1rem;
    text-decoration: none;
    display: flex;
    align-items: center;
    border-radius: 5px;
}
.sidebar a:hover, .sidebar a.active {
    background-color: #2563eb;
    color: white;
}
.sidebar a i {
    margin-right: 0.5rem;
}
.submenu {
    display: none;
    flex-direction: column;
    padding-left: 1.5rem;
}
.submenu a {
    padding: 0.5rem 0;
}

/* HEADER */
.header {
    position: fixed;
    left: 220px;
    top: 0;
    right: 0;
    height: 60px;
    background-color: white;
    border-bottom: 1px solid #ddd;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 1rem;
    z-index: 10;
}

/* MAIN */
.main {
    margin-left: 220px;
    margin-top: 70px;
    padding: 1rem 2rem;
}
.page-title {
    margin-bottom: 0.2rem;
}

/* ================= APPLICATION FLOW ================= */
.flow-wrapper {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}
.flow-step {
    background: #fff;
    padding: 1rem;
    border-radius: 12px;
    border: 2px solid transparent;
    flex: 1;
    min-width: 160px;
    cursor: pointer;
    transition: 0.3s;
}
.flow-step:hover {
    border-color: #2563eb;
    box-shadow: 0 0 12px rgba(37,99,235,0.4);
}
.flow-step.active {
    border-color: #22c55e;
    box-shadow: 0 0 15px rgba(34,197,94,0.6);
    background: #f0fdf4;
}
.flow-circle {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    text-align: center;
    line-height: 32px;
    font-weight: bold;
    color: white;
}
.completed { background: #22c55e; }
.current { background: #2563eb; }
.pending { background: #64748b; }

.flow-actions { display: none; font-size: 0.85rem; margin-top: 8px; }
.flow-actions.show { display: block; }

/* LEGEND */
.legend {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}
.legend div {
    display: flex;
    align-items: center;
    gap: 0.3rem;
}
.legend span {
    width: 20px;
    height: 10px;
    display: inline-block;
}

/* ICON BUTTON */
.icon-btn {
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1rem;
}

/* ================= SIDEBAR ================= */
.sidebar {
    width: 220px;
    height: 100vh;
    background: #fff;
    position: fixed;
    top: 0;
    left: 0;
    border-right: 1px solid #ddd;
    padding-top: 1rem;
}
.sidebar .logo {
    font-weight: bold;
    font-size: 1.2rem;
    text-align: center;
    margin-bottom: 2rem;
    color: #2563eb;
}
.sidebar a {
    color: #1e293b;
    padding: 0.75rem 1rem;
    display: flex;
    align-items: center;
    text-decoration: none;
    border-radius: 6px;
}
.sidebar a:hover, .sidebar a.active {
    background: #2563eb;
    color: #fff;
}
.submenu { display: none; padding-left: 1.5rem; }

/* ================= HEADER ================= */
.header {
    position: fixed;
    left: 220px;
    right: 0;
    top: 0;
    height: 60px;
    background: white;
    border-bottom: 1px solid #ddd;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0 1rem;
}

/* ================= MAIN ================= */
.main {
    margin-left: 220px;
    margin-top: 70px;
    padding: 1rem 2rem;
}

/* ================= STATUS CARDS ================= */
.status-summary {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    margin-bottom: 1.5rem;
}
.status-card {
    flex: 1;
    min-width: 130px;
    padding: 1rem;
    border-radius: 10px;
    background: #fff;
    border: 2px solid transparent;
    text-align: center;
    font-weight: bold;
    transition: 0.3s;
}
.status-card span {
    display: inline-block;
    padding: 4px 10px;
    border-radius: 6px;
    color: #fff;
    margin-top: 6px;
}

/* Colors */
.pending span { background: #facc15; }
.accepted span { background: #22c55e; }
.approved span { background: #2563eb; }
.rejected span { background: #ef4444; }
.payment-pending span { background: #f97316; }
.completed span { background: #10b981; }
.finalized span { background: #8b5cf6; }

/* Highlight effect */
.status-card:hover {
    transform: scale(1.05);
    box-shadow: 0 0 15px rgba(37,99,235,0.4);
    border-color: #2563eb;
}
footer {
    text-align: center;
    padding: 1rem;
    margin-top: 2rem;
    background: #fff;
    border-top: 1px solid #ddd;
    margin-left: 220px;
}
</style>
</head>

<body>

<!-- ===== SIDEBAR ===== -->
<div class="sidebar">
    <div class="logo">APARA Admin</div>

    <a href="#" class="active"><i class="bi bi-speedometer2"></i> Dashboard</a>

    <a href="javascript:void(0)" onclick="toggleMenu('appMenu')">
        <i class="bi bi-folder"></i> Applications
        <i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <div class="submenu" id="appMenu">
        <a href="#">Pending</a>
        <a href="#">Accepted</a>
        <a href="#">Approved</a>
        <a href="#">Rejected</a>
    </div>

    <a href="javascript:void(0)" onclick="toggleMenu('manageMenu')">
        <i class="bi bi-gear"></i> Management
        <i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <div class="submenu" id="manageMenu">
        <a href="#">Banks</a>
        <a href="#">Employees</a>
        <a href="#">Reports</a>
    </div>

    <a href="javascript:void(0)" onclick="toggleMenu('financeMenu')">
        <i class="bi bi-cash"></i> Finance
        <i class="bi bi-chevron-down ms-auto"></i>
    </a>
    <div class="submenu" id="financeMenu">
        <a href="#">Invoice</a>
        <a href="#">Fee Due</a>
        <a href="#">Receipt</a>
        <a href="#">History</a>
    </div>
</div>

<!-- ===== HEADER ===== -->
<div class="header">
    <div id="datetime"></div>
    <div>
        <i class="bi bi-bell text-primary me-3"></i>
        <strong>Admin User</strong>
    </div>
</div>

<!-- ===== MAIN DASHBOARD ===== -->
<div class="main">
    <h4 class="page-title">System Dashboard</h4>
    <p class="text-muted mb-4">Real-time APARA system overview</p>

    <!-- ===== STATUS SUMMARY ===== -->
    <div class="status-summary">
        <div class="status-card pending">Pending<br><span id="pendingCount">0</span></div>
        <div class="status-card accepted">Accepted<br><span id="acceptedCount">0</span></div>
        <div class="status-card approved">Approved<br><span id="approvedCount">0</span></div>
        <div class="status-card rejected">Rejected<br><span id="rejectedCount">0</span></div>
        <div class="status-card payment-pending">Payment Pending<br><span id="paymentPendingCount">0</span></div>
        <div class="status-card completed">Completed Payments<br><span id="completedCount">0</span></div>
        <div class="status-card finalized">Finalized<br><span id="finalizedCount">0</span></div>
    </div>

    <!-- APPLICATION WORKFLOW -->
    <h5 class="mt-4">Application Workflow</h5>
    <div class="flow-wrapper">
      <!-- STEP 1 -->
      <div class="flow-step" data-step="submission">
        <div class="flow-circle completed">1</div>
        <div class="flow-title">Submission</div>
        <div class="flow-actions" id="submissionActions">
            Fill form<br>
            Upload documents<br>
            Submit application
       </div>
      </div>
      <!-- STEP 2 -->
      <div class="flow-step" data-step="validation">
        <div class="flow-circle current">2</div>
        <div class="flow-title">Validation</div>
        <div class="flow-actions" id="validationActions">
            Check data accuracy<br>
            Verify attachments<br>
            Confirm eligibility
        </div>
      </div>
      <!-- STEP 3 -->
      <div class="flow-step" data-step="review">
        <div class="flow-circle pending">3</div>
        <div class="flow-title">Review</div>
        <div class="flow-actions" id="reviewActions">
            Assign reviewer<br>
            Evaluate application<br>
            Add comments
        </div>
      </div>
      <!-- STEP 4 -->
      <div class="flow-step" data-step="approval">
        <div class="flow-circle pending">4</div>
        <div class="flow-title">Approval</div>
        <div class="flow-actions" id="approvalActions">
            Approve or reject<br>
            Notify applicant
        </div>
      </div>
      <!-- STEP 5 -->
      <div class="flow-step" data-step="processing">
        <div class="flow-circle pending">5</div>
        <div class="flow-title">Processing</div>
        <div class="flow-actions" id="processingActions">
            Issue documents<br>
            Generate receipts<br>
            Update records
        </div>
      </div>
      <!-- STEP 6 -->
      <div class="flow-step" data-step="completion">
        <div class="flow-circle pending">6</div>
        <div class="flow-title">Completion</div>
        <div class="flow-actions" id="completionActions">
            Archive application<br>
            Close case<br>
            Send final report
        </div>
      </div>
    </div>

    <!-- LEGEND -->
    <div class="legend">
        <div><span style="background:#22c55e"></span>Completed</div>
        <div><span style="background:#2563eb"></span>Current</div>
        <div><span style="background:#64748b"></span>Pending</div>
    </div>

    <!-- ACTION BAR -->
    <div class="action-bar mt-4">
        <div class="d-flex gap-2 mb-2">
            <button class="btn btn-light border"><i class="bi bi-plus-circle"></i> New Application</button>
            <button class="btn btn-light border"><i class="bi bi-download"></i> Export</button>
            <button class="btn btn-light border"><i class="bi bi-info-circle"></i> Additional Info</button>
        </div>
        <div class="input-group" style="width:300px;">
            <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
            <input type="text" class="form-control" placeholder="Search By ID Number">
        </div>
    </div>

    <!-- TABLE -->
    <div class="card shadow-sm mt-4">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Application No</th>
                        <th>Proposal No</th>
                        <th>Name</th>
                        <th>ID Number</th>
                        <th>Edit</th>
                        <th>View</th>
                        <th>Ack</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Invoice</th>
                        <th>Receipt</th>
                        <th>Acceptance Letter</th>
                        <th>Schedule</th>
                    </tr>
                </thead>
                <tbody id="applicationTable">
                    <tr>
                        <td>APP-0012</td>
                        <td>PR-556</td>
                        <td>ABC Exports</td>
                        <td>902134567V</td>
                        <td><button class="icon-btn"><i class="bi bi-pencil-square"></i></button></td>
                        <td><button class="icon-btn"><i class="bi bi-eye"></i></button></td>
                        <td><button class="icon-btn"><i class="bi bi-check2-square"></i></button></td>
                        <td><span class="badge bg-warning">Pending</span></td>
                        <td><button class="icon-btn"><i class="bi bi-credit-card"></i></button></td>
                        <td><button class="icon-btn"><i class="bi bi-file-earmark-text"></i></button></td>
                        <td><button class="icon-btn"><i class="bi bi-receipt"></i></button></td>
                        <td><button class="icon-btn"><i class="bi bi-envelope-check"></i></button></td>
                        <td><button class="icon-btn"><i class="bi bi-calendar-event"></i></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- FOOTER -->
<footer>
    © 2025 Sri Lanka Export Credit Insurance Corporation | APARA System
</footer>

<script>
// Toggle Sidebar Menus
function toggleMenu(id) {
    let menu = document.getElementById(id);
    menu.style.display = (menu.style.display === "flex") ? "none" : "flex";
}

// Update Date & Time
function updateDateTime() {
    document.getElementById("datetime").innerText = new Date().toLocaleString();
}
setInterval(updateDateTime, 1000);
updateDateTime();

// FLOW STEP CLICK FUNCTIONALITY
const flowSteps = document.querySelectorAll('.flow-step');
flowSteps.forEach(step => {
    step.addEventListener('click', () => {
        const stepName = step.dataset.step;
        document.querySelectorAll('.flow-actions').forEach(action => action.classList.remove('show'));
        document.getElementById(stepName + 'Actions').classList.add('show');
    });
});

// DYNAMIC STATUS COUNTS
function updateStatusCounts() {
    const table = document.getElementById('applicationTable');
    const rows = table.querySelectorAll('tr');
    let pending = 0, accepted = 0, approved = 0, rejected = 0, paymentPending = 0, completed = 0, finalized = 0;

    rows.forEach(row => {
        const statusText = row.cells[7].innerText.toLowerCase();
        const paymentIcon = row.cells[8].querySelector('i');
        
        if(statusText.includes('pending')) pending++;
        if(statusText.includes('accepted')) accepted++;
        if(statusText.includes('approved')) approved++;
        if(statusText.includes('rejected')) rejected++;
        if(paymentIcon && paymentIcon.classList.contains('bi-credit-card')) paymentPending++;
        
        const receiptIcon = row.cells[10].querySelector('i');
        const acceptanceIcon = row.cells[11].querySelector('i');
        if(receiptIcon && receiptIcon.classList.contains('bi-receipt')) completed++;
        if(acceptanceIcon && acceptanceIcon.classList.contains('bi-envelope-check')) finalized++;
    });

    document.getElementById('pendingCount').innerText = pending;
    document.getElementById('acceptedCount').innerText = accepted;
    document.getElementById('approvedCount').innerText = approved;
    document.getElementById('rejectedCount').innerText = rejected;
    document.getElementById('paymentPendingCount').innerText = paymentPending;
    document.getElementById('completedCount').innerText = completed;
    document.getElementById('finalizedCount').innerText = finalized;
}

updateStatusCounts();
</script>

</body>
</html>
