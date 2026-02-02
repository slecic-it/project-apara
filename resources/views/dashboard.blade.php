<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>APARA | Admin Dashboard</title>

<!-- Bootstrap -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link rel="stylesheet" href="css/dashboard.css"/>

<style>

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
                <tbody>
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
        
        // Hide all actions first
        document.querySelectorAll('.flow-actions').forEach(action => action.classList.remove('show'));
        
        // Show only clicked step actions
        document.getElementById(stepName + 'Actions').classList.add('show');
    });
});
</script>

</body>
</html>
